<?php

namespace Tests\Application\Jobs;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\SendDueReminders;
use App\Services\Scheduler\Kernel;

class SendDueRemindersTest extends TestCase
{
    /** @test */
    public function it_only_sends_reminder_if_initial_reminder_has_not_run_yet()
    {
        // This Reminder has already run
        $this->createReminderFor($user = $this->signIn())->markInitialReminderComplete();

        // This Reminder hasn't run yet
        $this->createReminderFor($user);

        (new SendDueReminders(
            $kernel = new Kernel(),
            $this->app->getContainer()
        ))->run();

        $this->assertCount(1, $kernel->getTasks());
    }
}
