<?php

namespace Tests\Application\SchedulerJobs;

use Carbon\Carbon;
use Tests\TestCase;
use App\Services\Scheduler\Kernel;
use App\SchedulerJobs\AddDueReminders;

class AddDueRemindersTest extends TestCase
{
    /** @test */
    public function only_sends_reminders_where_next_run_datetime_has_past()
    {
        $tz = 'Europe/London';

        $shouldBeIncluded = [
            // In past
            $this->createReminder(1, Carbon::create(1988, 1, 26, 9, 45, 0, $tz)),
            $this->createReminder(1, Carbon::create(1988, 1, 26, 9, 45, 0, $tz)),

            // Now - This is technically past so should be included
            $this->createReminder(1, Carbon::now($tz))
        ];

        // In future - These should NOT be included
        $shouldNotBeIncluded = [
            $this->createReminder(1, Carbon::now($tz)->addMinutes(5)),
            $this->createReminder(1, Carbon::now($tz)->addMonth()),
        ];

        (new AddDueReminders(
            $kernel = new Kernel(),
            $this->app->getContainer()
        ))->run();

        $this->assertCount(count($shouldBeIncluded), $kernel->getTasks());
    }

    /** @test */
    public function recurring_reminders_are_added()
    {
        $tz = 'Europe/London';
        $reminder = $this->createReminder(1, Carbon::now($tz)->subDay());
        $reminder->initial_reminder_run = true;
        $reminder->is_recurring = true;
        $reminder->frequency = "daily";
        $reminder->save();

        // Initial reminder has run
        $this->assertTrue($reminder->hasInitialReminderRun());
        // Is recurring
        $this->assertTrue($reminder->isRecurring());
        
        (new AddDueReminders(
            $kernel = new Kernel(),
            $this->app->getContainer()
        ))->run();

        $this->assertCount(1, $kernel->getTasks());
    }

    /** @test */
    public function non_recurring_reminders_are_not_added()
    {
        $tz = 'Europe/London';
        $reminder = $this->createReminder(1, Carbon::now($tz)->subDay());
        $reminder->markInitialReminderComplete();
        
        // Initial reminder has run
        $this->assertTrue($reminder->hasInitialReminderRun());
        // No recurrence
        $this->assertFalse($reminder->isRecurring());
        
        (new AddDueReminders(
            $kernel = new Kernel(),
            $this->app->getContainer()
        ))->run();

        $this->assertEmpty($kernel->getTasks());
    }
}
