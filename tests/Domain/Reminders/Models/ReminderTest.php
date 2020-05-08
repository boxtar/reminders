<?php

namespace Tests\Domain\Reminders\Models;

use App\Domain\Dates\DatesSupport;
use App\Domain\Recurrences\RecurrencesSupport;
use App\Domain\Reminders\Models\Reminder;
use App\Models\User;
use Tests\TestCase;

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
}
