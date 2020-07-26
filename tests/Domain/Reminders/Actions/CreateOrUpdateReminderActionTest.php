<?php

namespace Tests\Domain\Reminders\Actions;

use Tests\TestCase;
use Carbon\Carbon;
use App\Domain\Reminders\ReminderData;
use App\Domain\Reminders\Models\Reminder;
use App\Domain\Recurrences\RecurrencesSupport;
use App\Domain\Reminders\Actions\CreateOrUpdateReminderAction;

class CreateOrUpdateReminderActionTest extends TestCase
{
    /** @test */
    public function can_create_from_static_factory()
    {
        $this->assertInstanceOf(CreateOrUpdateReminderAction::class, CreateOrUpdateReminderAction::create());
    }

    /** @test */
    public function body_is_required_when_creating()
    {
        // ReminderData with data for creating
        $reminderData = $this->makeReminderData();
        // Remove the body to test validation
        $reminderData->body = "";
        // Execute the creation action
        $response = CreateOrUpdateReminderAction::create()->execute($reminderData, $this->signIn());

        $this->assertEquals(400, $response->getStatus());
        $this->assertCount(1, $response->getErrors());
        $this->assertArrayHasKey('body', $response->getErrors());
    }

    /** @test */
    public function creating_a_reminder_sets_the_next_run_date()
    {
        $reminderDate = Carbon::now('Europe/London')->addDays(1);
        $reminder = $this->createReminderFor($this->signIn(), 1, $reminderDate);
        $this->assertEquals($reminderDate->format('Y-m-d H:i'), $reminder->next_run->format('Y-m-d H:i'));
    }

    /** @test */
    public function body_is_required_when_updating()
    {
        // Original reminder
        $reminder = $this->createReminderFor($user = $this->signIn(), 1);
        // Updated data
        $reminderData = $this->makeReminderData();
        // Take body out to test validation
        $reminderData->body = "";
        // Execute update action
        $response = CreateOrUpdateReminderAction::create()->execute($reminderData, $user, $reminder);

        $this->assertEquals(400, $response->getStatus());
        $this->assertCount(1, $response->getErrors());
        $this->assertArrayHasKey('body', $response->getErrors());
    }

    /** @test */
    public function sets_status_and_data_on_success()
    {
        // Original reminder
        $reminder = $this->createReminderFor($user = $this->signIn(), 1);

        // Updated data to be applied to original
        $updatedData = $this->makeReminderData();
        $updatedData->body = 'Updated Reminder';

        // Execute the update action
        $response = CreateOrUpdateReminderAction::create()->execute($updatedData, $user, $reminder);

        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals("Updated Reminder", $response->getData()['body']);
    }

    /** @test */
    public function can_update_body_without_affecting_other_attribs()
    {
        $user = $this->signIn();
        // Original reminder (we need the ReminderData to create updated data from it)
        $originalData = $this->makeReminderData();
        $response = CreateOrUpdateReminderAction::create()->execute($originalData, $user);
        $reminder = Reminder::find(1);

        // Set date in the past and setup so that initial reminder has already run
        $reminder->date = 1;
        $reminder->month = 0;
        $reminder->year = 2020;
        $reminder->initial_reminder_run = true;

        // Updated data will be an clone of the original data so that we can make sure
        // only the body is updated
        $updatedData = ReminderData::createFromArray($originalData->toArray());
        $updatedData->body = "Updated Reminder";

        // Execute update action
        $response = CreateOrUpdateReminderAction::create()->execute($updatedData, $user, $reminder);

        // Assert body was updated
        $this->assertEquals("Updated Reminder", $response->getData()['body']);

        // Assert other attributes remains unchanged
        $this->assertEquals($reminder->day, $response->getData()['day']);
        $this->assertEquals($reminder->date, $response->getData()['date']);
        $this->assertEquals($reminder->month, $response->getData()['month']);
        $this->assertEquals($reminder->year, $response->getData()['year']);
        $this->assertEquals($reminder->hour, $response->getData()['hour']);
        $this->assertEquals($reminder->minute, $response->getData()['minute']);
        $this->assertEquals($reminder->expression, $response->getData()['expression']);
        $this->assertEquals($reminder->initial_reminder_run, $response->getData()['initial_reminder_run']);
        $this->assertEquals($reminder->recurrence_expression, $response->getData()['recurrence_expression']);
        $this->assertEquals($reminder->is_recurring, $response->getData()['is_recurring']);
        $this->assertEquals($reminder->channels, $response->getData()['channels']);
    }

    /** @test */
    public function can_update_reminder_date()
    {
        // Original reminder
        $reminder = $this->createReminderFor($user = $this->signIn());
        // Mark it as complete
        $reminder->markInitialReminderComplete();

        // Updated data - Use a date 1 year in the future
        $oneYearInFuture = Carbon::now('Europe/London')->addYear();
        $updatedData = $this->makeReminderData($oneYearInFuture);

        // Update the date and time of the reminder. This should update the expression
        // and the initial_reminder_run flag to false (as the date has been moved on to a future date).
        $response = CreateOrUpdateReminderAction::create()->execute($updatedData, $user, $reminder);
        
        $this->assertEquals(200, $response->getStatus());
        // The reminder should have the updated attributes now
        $this->assertEquals($reminder->body, $response->getData()['body']);
        $this->assertEquals($oneYearInFuture->ordinal('day'), $reminder->date);
        $this->assertEquals($oneYearInFuture->monthName, $reminder->month);
        $this->assertEquals($oneYearInFuture->year, $reminder->year);
        $this->assertEquals($oneYearInFuture->hour, $reminder->hour);
        $this->assertEquals($oneYearInFuture->minute, $reminder->minute);
        $this->assertEquals($oneYearInFuture->format('Y-m-d H:i'), $reminder->next_run->format('Y-m-d H:i'));
        $this->assertEquals("{$oneYearInFuture->minute} {$oneYearInFuture->hour} {$oneYearInFuture->day} {$oneYearInFuture->month} *", $reminder->expression);
        $this->assertEquals(false, $reminder->initial_reminder_run);
    }

    /** @test */
    public function initial_reminder_flag_remains_true_if_updated_date_is_in_the_past()
    {
        // Test can update reminder date, but if updated date is also in the past then 
        // initial_reminder_run is NOT changed to false. This would stop any recurrence
        // from triggering and the initial reminder will never run as it's in the past.

        // Original reminder
        $reminder = $this->createReminderFor($user = $this->signIn(), 1, Carbon::parse('2019-01-01', 'Europe/London'));
        $reminder->markInitialReminderComplete();

        // Updated data
        // 1 year in the future, but still in the past.
        $updatedData = $this->makeReminderData(Carbon::parse('2020-01-31 00:00:00', 'Europe/London'));

        // Update the date and time of the reminder. This should update the expression
        // and the initial_reminder_run flag to false (as the date has been moved on).
        $response = CreateOrUpdateReminderAction::create()->execute($updatedData, $user, $reminder);

        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals(2020, $reminder->year);
        $this->assertEquals("0 0 31 1 *", $reminder->expression);
        $this->assertEquals(true, $reminder->initial_reminder_run);
    }

    // Test can update reminder frequency. It should update 'recurrence_expression' and
    // 'is_recurring' accordingly.
    /** @test */
    public function can_update_frequency()
    {
        // Original reminder (with no recurrence)
        $reminder = $this->createReminderFor($user = $this->signIn());
        $this->assertEquals(false, $reminder->is_recurring);
        $this->assertEquals("No recurrence", $reminder->frequency);

        // Updated data with recurrence
        $newData = $this->makeReminderData();
        $newData->frequency = "quarterly";
        $response = CreateOrUpdateReminderAction::create()->execute($newData, $user, $reminder);

        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals(true, $reminder->is_recurring);
        $this->assertEquals(RecurrencesSupport::frequencies()[$newData->frequency], $reminder->frequency);
    }

    /** @test */
    public function can_update_channels()
    {
        // Original reminder - This defaults to only mail channel
        $reminder = $this->createReminderFor($user = $this->signIn());
        $this->assertCount(1, $reminder->channels);
        $this->assertEquals('mail', $reminder->channels[0]);

        // Updated data with recurrence
        $newData = $this->makeReminderData();
        $newData->channels = ['mail', 'sms'];
        $response = CreateOrUpdateReminderAction::create()->execute($newData, $user, $reminder);

        $this->assertEquals(200, $response->getStatus());
        $this->assertCount(2, $reminder->channels);
        $this->assertEquals('mail', $reminder->channels[0]);
        $this->assertEquals('sms', $reminder->channels[1]);
    }
}
