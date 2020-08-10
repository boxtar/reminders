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

        // Sunday 19th July 2020 GMT - Ava!
        $reminder->next_run = Carbon::create(2020, 7, 19, 6, 0, 0, 'Europe/London');
        $this->assertEquals("Sunday", $reminder->day);

        // Saturday 01st August 2020 GMT - Day on which I'm updating this test.
        $reminder->next_run = Carbon::create(2020, 8, 1, 10, 13, 0, 'Europe/London');
        $this->assertEquals("Saturday", $reminder->day);

        // Thursday 05th November 2020 GMT - Bonfire night!
        $reminder->next_run = Carbon::create(2020, 11, 5, 18, 30, 0, 'Europe/London');
        $this->assertEquals("Thursday", $reminder->day);
    }

    /** @test */
    public function date_accessor_returns_expected_result()
    {
        $reminder = $this->makeReminder();

        // Sunday 19th July 2020 GMT - Ava!
        $reminder->next_run = Carbon::create(2020, 7, 19, 6, 0, 0, 'Europe/London');
        $this->assertEquals("19th", $reminder->date);

        // Saturday 01st August 2020 GMT - Day on which I'm updating this test.
        $reminder->next_run = Carbon::create(2020, 8, 1, 10, 13, 0, 'Europe/London');
        $this->assertEquals("1st", $reminder->date);

        // Thursday 05th November 2020 GMT - Bonfire night!
        $reminder->next_run = Carbon::create(2020, 11, 5, 18, 30, 0, 'Europe/London');
        $this->assertEquals("5th", $reminder->date);
    }

    /** @test */
    public function month_accessor_returns_expected_result()
    {
        $reminder = $this->makeReminder();

        // Sunday 19th July 2020 GMT - Ava!
        $reminder->next_run = Carbon::create(2020, 7, 19, 6, 0, 0, 'Europe/London');
        $this->assertEquals("July", $reminder->month);

        // Saturday 01st August 2020 GMT - Day on which I'm updating this test.
        $reminder->next_run = Carbon::create(2020, 8, 1, 10, 13, 0, 'Europe/London');
        $this->assertEquals("August", $reminder->month);

        // Thursday 05th November 2020 GMT - Bonfire night!
        $reminder->next_run = Carbon::create(2020, 11, 5, 18, 30, 0, 'Europe/London');
        $this->assertEquals("November", $reminder->month);
    }

    /** @test */
    public function test_reminder_year_accessor()
    {
        $reminder = $this->makeReminder();
        $reminder->setRecurrenceFrequency("yearly");

        // Sunday 19th July 2020 GMT - Ava!
        $reminder->next_run = Carbon::create(2020, 7, 19, 6, 0, 0, 'Europe/London');
        $this->assertEquals(2020, $reminder->year);

        $reminder->forwardNextRunDate();
        $this->assertEquals(2021, $reminder->year);

    }

    /** @test */
    public function test_reminder_hour_accessor()
    {
        $reminder = $this->makeReminder();

        // Sunday 19th July 2020 GMT - Ava!
        $reminder->next_run = Carbon::create(2020, 7, 19, 6, 0, 0, 'Europe/London');
        $this->assertEquals(6, $reminder->hour);

        // Saturday 01st August 2020 GMT - Day on which I'm updating this test.
        $reminder->next_run = Carbon::create(2020, 8, 1, 10, 13, 0, 'Europe/London');
        $this->assertEquals(10, $reminder->hour);

        // Thursday 05th November 2020 GMT - Bonfire night!
        $reminder->next_run = Carbon::create(2020, 11, 5, 18, 30, 0, 'Europe/London');
        $this->assertEquals(18, $reminder->hour);
    }

    /** @test */
    public function test_reminder_minute_accessor()
    {
        $reminder = $this->makeReminder();

        // Sunday 19th July 2020 GMT - Ava!
        $reminder->next_run = Carbon::create(2020, 7, 19, 6, 0, 0, 'Europe/London');
        $this->assertEquals(0, $reminder->minute);

        // Saturday 01st August 2020 GMT - Day on which I'm updating this test.
        $reminder->next_run = Carbon::create(2020, 8, 1, 10, 13, 0, 'Europe/London');
        $this->assertEquals(13, $reminder->minute);

        // Thursday 05th November 2020 GMT - Bonfire night!
        $reminder->next_run = Carbon::create(2020, 11, 5, 18, 30, 0, 'Europe/London');
        $this->assertEquals(30, $reminder->minute);
    }

    /** @test */
    public function test_reminder_date_accessor()
    {
        $reminder = $this->makeReminder();

        // Sunday 19th July 2020 GMT - Ava!
        $reminder->next_run = Carbon::create(2020, 7, 19, 6, 0, 0, 'Europe/London');
        $this->assertEquals("202007190600", $reminder->reminderDate);

        // Saturday 01st August 2020 GMT - Day on which I'm updating this test.
        $reminder->next_run = Carbon::create(2020, 8, 1, 10, 13, 0, 'Europe/London');
        $this->assertEquals("202008011013", $reminder->reminderDate);

        // Thursday 05th November 2020 GMT - Bonfire night!
        $reminder->next_run = Carbon::create(2020, 11, 5, 18, 30, 0, 'Europe/London');
        $this->assertEquals("202011051830", $reminder->reminderDate);
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
        $initialDate = Carbon::parse($initialDate->format("Y/m/d H:i"));
        $reminder = $this->createReminder(1, $initialDate);

        // Try a day first
        $reminder->setRecurrenceFrequency("daily");
        $reminder->forwardNextRunDate();
        $this->assertEquals(
            $initialDate->addDay(),
            $reminder->next_run
        );

        // Now try a week
        $reminder->setRecurrenceFrequency("weekly");
        $reminder->forwardNextRunDate();
        $this->assertEquals(
            $initialDate->addWeek(),
            $reminder->next_run
        );

        // That's good enough. We've already tested the forwarding logic in RecurrenceSupport.
    }

    /** @test */
    public function if_forwarded_date_is_in_past_then_forward_based_on_current_time()
    {
        // A week ago
        $initialDate = Carbon::now('Europe/London')->subWeek();
        $initialDate = Carbon::parse($initialDate->format("Y/m/d H:i"));
        $reminder = $this->createReminder(1, $initialDate);

        // Set daily recurrence so that when it is forwarded, it's still in the past
        $reminder->setRecurrenceFrequency("daily");
        $reminder->forwardNextRunDate();

        $expectedNewDate = Carbon::parse(
            Carbon::now('Europe/London')->addDay()->format("Y/m/d H:i")
        );

        $this->assertEquals($expectedNewDate, $reminder->next_run);
    }

}
