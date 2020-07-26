<?php

namespace Tests\Domain\Reminders\Models;

use App\Domain\Recurrences\Exceptions\InvalidRecurrenceException;
use Tests\TestCase;
use App\Domain\Recurrences\RecurrencesSupport;
use App\Domain\Reminders\Actions\CreateOrUpdateReminderAction;
use App\Domain\Reminders\Models\Reminder;
use Carbon\Carbon;

class ReminderTest extends TestCase
{
    /** @test */
    public function frequency_accessor_returns_expected_result()
    {
        $reminder = $this->makeReminder();
        $this->assertEquals("No recurrence", $reminder->frequency);

        $reminder->is_recurring = true;
        foreach (RecurrencesSupport::frequencies() as $key => $value) {
            $reminder->frequency = $key;
            $this->assertEquals($value, $reminder->frequency);
        }
    }

    /** @test */
    public function next_run_field_is_cast_to_a_date()
    {
        $this->createReminder();
        $this->assertInstanceOf(Carbon::class, Reminder::first()->next_run);
    }

    /** @test */
    public function day_accessor_returns_expected_result()
    {
        $reminder = $this->makeReminder();

        $reminder->day = 7; // Invalid day - test app does not break on invalid day
        $this->assertEquals("Invalid day", $reminder->day);

        $reminder->day = 0; // Sunday
        $this->assertEquals("Sunday", $reminder->day);

        $reminder->day = 1; // Monday
        $this->assertEquals("Monday", $reminder->day);
        // Etc. This is not a days test. That is for the Dates domain
    }

    /** @test */
    public function date_accessor_returns_expected_result()
    {
        $reminder = $this->makeReminder();

        // If date is falsy, assert false is return and app does not break.
        $reminder->date = null;
        $this->assertEquals("Invalid date", $reminder->date);

        $reminder->date = 1; // 1st
        $this->assertEquals("1st", $reminder->date);

        $reminder->date = 2; // 2nd
        $this->assertEquals("2nd", $reminder->date);
        // Etc.. this is NOT an ordinal date test
    }

    /** @test */
    public function month_accessor_returns_expected_result()
    {
        $reminder = $this->makeReminder();

        // Falsy month should not break app.
        $reminder->month = null;
        $this->assertEquals("Invalid month", $reminder->month);
        $reminder->month = false;
        $this->assertEquals("Invalid month", $reminder->month);

        $reminder->month = 0;
        $this->assertEquals("January", $reminder->month);
        $reminder->month = 11;
        $this->assertEquals("December", $reminder->month);
    }

    /** @test */
    public function test_reminder_date_accessor()
    {
        $reminder = $this->makeReminder();
        $reminder->year = 2020;
        $reminder->month = 6;
        $reminder->date = 8;
        $reminder->hour = 9;
        $reminder->minute = 0;
        $this->assertEquals("202006080900", $reminder->reminderDate);
    }

    /** @test */
    public function indicates_if_initial_reminder_has_run()
    {
        $reminder = $this->makeReminder();
        $this->assertFalse($reminder->hasInitialReminderRun());
        $reminder->initial_reminder_run = true;
        $this->assertTrue($reminder->hasInitialReminderRun());
    }

    /** @test */
    public function indicates_if_reminder_is_recurring()
    {
        $reminder = $this->makeReminder();
        $this->assertFalse($reminder->isRecurring());
        $reminder->is_recurring = true;
        $this->assertTrue($reminder->isRecurring());
    }

    /** @test */
    public function returns_initial_reminder_expression_if_initial_has_not_run_yet()
    {
        $initialExpression = "0 9 1 1 *"; // 1st Jan at 9am

        $reminder = $this->makeReminder();
        $reminder->expression = $initialExpression;


        // As initial reminder hasn't run this should return the initial expression
        $this->assertEquals($initialExpression, $reminder->getCronExpression());

        // Mark initial reminder as complete.
        // is_recurring is null, so this should return false now.
        $reminder->initial_reminder_run = true;
        $this->assertFalse($reminder->getCronExpression());

        // Now set is_recurring to true. This should cause the
        // recurring expression to return.
        $reminder->is_recurring = true;
    }

    /** @test */
    public function has_initial_reminder_run_works_correctly()
    {
        $reminder = $this->createReminder();
        $reminder->initial_reminder_run = false; // it defaults to this, but just being explicit
        $this->assertFalse($reminder->hasInitialReminderRun());
        $reminder->initial_reminder_run = true;
        $this->assertTrue($reminder->hasInitialReminderRun());
    }

    /** @test */
    public function mark_initial_reminder_as_run_works_correctly()
    {
        $reminder = $this->createReminder();
        $this->assertFalse($reminder->hasInitialReminderRun());
        $reminder->markInitialReminderComplete();
        $this->assertTrue($reminder->hasInitialReminderRun());
    }

    /** @test */
    public function is_recurring_works_correctly()
    {
        $reminder = $this->createReminder();
        $reminder->is_recurring = false; // it defaults to this, but just being explicit
        $this->assertFalse($reminder->isRecurring());
        $reminder->is_recurring = true;
        $this->assertTrue($reminder->isRecurring());
    }

    /** @test */
    public function set_recurrence_works_correctly()
    {
        $reminder = $this->createReminder();
        $this->assertFalse($reminder->isRecurring());
        $this->assertEquals("No recurrence", $reminder->frequency);

        // Set from no recurrence to monthly recurrence
        $reminder->setRecurrenceFrequency("monthly");
        $this->assertTrue($reminder->isRecurring());
        $this->assertEquals("Every month", $reminder->frequency);

        // Invalid frequency throws an exception (fail fast principle)
        // Also, assert model is not affected
        $this->expectException(InvalidRecurrenceException::class);
        $reminder->setRecurrenceFrequency("invalid frequency");
        // Assert no change
        $this->assertTrue($reminder->isRecurring());
        $this->assertEquals("Every month", $reminder->frequency);
    }

    /** @test */
    public function remove_recurrence_works_correctly()
    {
        // $reminder->removeRecurrence()
        // $reminder->isRecurring() = false
        // $reminder->frequency() = No recurrence
        $reminder = $this->createReminder();
        $reminder->setRecurrenceFrequency("monthly");
        $this->assertTrue($reminder->isRecurring());
        $this->assertEquals("Every month", $reminder->frequency);

        $reminder->removeRecurrence();
        $this->assertFalse($reminder->isRecurring());
        $this->assertEquals("No recurrence", $reminder->frequency);
    }

    /** @test */
    public function reminder_can_be_sent_if_initial_has_not_run_yet()
    {
        /**
         * Create reminder with no reccurence that hasn't run yet and
         * assert it can be sent.
         */
        $user = $this->signIn();
        $reminderData = $this->makeReminderData(Carbon::now('Europe/London'));
        CreateOrUpdateReminderAction::create()->execute($reminderData, $user);
        $reminder = Reminder::first();
        $this->assertFalse($reminder->hasInitialReminderRun());
        $this->assertFalse($reminder->isRecurring());
        $this->assertCount(1, Reminder::canBeSent()->get());

        /**
         * Update the reminder with a daily recurrence and assert can still be sent
         */
        $reminder->frequency = "daily";
        $reminder->is_recurring = true;
        $this->assertFalse($reminder->hasInitialReminderRun());
        $this->assertTrue($reminder->isRecurring());
        $this->assertCount(1, Reminder::canBeSent()->get());
    }

    /** @test */
    public function reminder_can_be_sent_if_initial_has_been_sent_and_is_recurring()
    {
        $user = $this->signIn();
        $reminderData = $this->makeReminderData();
        // Give recurrence
        $reminderData->frequency = "daily";
        CreateOrUpdateReminderAction::create()->execute($reminderData, $user);
        $reminder = Reminder::first();
        // Mark initial run as completed
        $reminder->markInitialReminderComplete();
        // Make sure this can still be sent
        $this->assertCount(1, Reminder::canBeSent()->get());
    }

    /** @test */
    public function reminder_cannot_be_sent_if_initial_has_run_and_not_recurring()
    {
        $user = $this->signIn();
        // Default reminder data has no recurrence
        CreateOrUpdateReminderAction::create()->execute($this->makeReminderData(), $user);
        $reminder = Reminder::first();
        // Mark initial run as completed
        $reminder->markInitialReminderComplete();
        // Make sure this CANNOT be sent
        $this->assertCount(0, Reminder::canBeSent()->get());
    }

    /** @test */
    public function can_update_next_run_based_on_frequency()
    {
        $initialDate = Carbon::now('Europe/London');
        $reminder = $this->createReminder(1, $initialDate);

        // Try a day first
        $reminder->setRecurrenceFrequency("daily");
        $reminder->forwardNextRunDate();
        $this->assertEquals(
            $initialDate->addDay()->format('Y-m-d h:m'),
            $reminder->next_run->format('Y-m-d h:m')
        );

        // Now try a week
        $reminder->setRecurrenceFrequency("weekly");
        $reminder->forwardNextRunDate();
        $this->assertEquals(
            $initialDate->addWeek()->format('Y-m-d h:m'),
            $reminder->next_run->format('Y-m-d h:m')
        );

        // That's good enough. We've already tested the forwarding logic in RecurrenceSupport.
    }
}
