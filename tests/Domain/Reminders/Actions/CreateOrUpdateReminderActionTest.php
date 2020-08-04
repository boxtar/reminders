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
        $response = CreateOrUpdateReminderAction::create()->execute($this->makeReminderData($reminderDate), $this->signIn());
        $data = (object) $response->getData();
        $reminder = Reminder::find($data->id);
        $this->assertEquals($reminderDate->format('Y-m-d H:i'), $reminder->next_run->format('Y-m-d H:i'));
    }

    /** @test */
    public function creating_a_reminder_defaults_initial_reminder_run_to_false()
    {
        $response = CreateOrUpdateReminderAction::create()->execute($this->makeReminderData(), $this->signIn());
        $data = (object) $response->getData();
        $reminder = Reminder::find($data->id);
        $this->assertFalse($reminder->hasInitialReminderRun());
    }

    /** @test */
    public function creating_a_reminder_with_an_invalid_frequency_sets_to_not_recurring()
    {
        $reminderData = $this->makeReminderData();
        $reminderData->frequency = "Utter Nonsense";
        $resp = (object) CreateOrUpdateReminderAction::create()
            ->execute($reminderData, $this->signIn())
            ->getData();
        $reminder = Reminder::find($resp->id);

        $this->assertFalse($reminder->isRecurring());
        $this->assertEquals("No recurrence", $reminder->frequency);
    }

    /** @test */
    public function creating_a_reminder_with_a_valid_frequency_sets_to_recurring()
    {
        $reminderData = $this->makeReminderData();
        $reminderData->frequency = "yearly";
        $resp = (object) CreateOrUpdateReminderAction::create()
            ->execute($reminderData, $this->signIn())
            ->getData();
        $reminder = Reminder::find($resp->id);

        $this->assertTrue($reminder->isRecurring());
        $this->assertEquals("Every year", $reminder->frequency);
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
        $reminder->markInitialReminderComplete();

        // Updated data will be a clone of the original data so that we can make sure
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
        $this->assertEquals(false, $reminder->initial_reminder_run);
    }

    /** @test */
    public function initial_reminder_flag_stays_the_same_if_date_is_not_changing()
    {
        // Reminder with initial reminder flag set to false
        $reminder = $this->createReminderFor($user = $this->signIn(), 1, $date = Carbon::now());

        // Updated data using the exact same date as the original reminder
        $updatedData = $this->makeReminderData($date);

        // Do the update
        CreateOrUpdateReminderAction::create()->execute($updatedData, $user, $reminder);

        // Initial reminder flag should still be false
        $this->assertFalse($reminder->hasInitialReminderRun());

        // Now flip initial reminder flag to true
        $reminder->markInitialReminderComplete();

        // Do the update again
        CreateOrUpdateReminderAction::create()->execute($updatedData, $user, $reminder);

        // Initial reminder flag should have remained true
        $this->assertTrue($reminder->hasInitialReminderRun());
    }

    /** @test */
    public function initial_reminder_flag_is_set_to_false_if_date_changes()
    {
        // Reminder with initial reminder flag set to true
        $reminder = $this->createReminderFor($user = $this->signIn(), 1, Carbon::now());
        $reminder->markInitialReminderComplete();

        // Update the reminder with a new date. This should make the initial reminder flag false.
        CreateOrUpdateReminderAction::create()
            ->execute(
                $this->makeReminderData(Carbon::now()->addDay()),
                $user,
                $reminder
            );

        // Initial reminder flag should now be false
        $this->assertFalse($reminder->hasInitialReminderRun());

        // Update the reminder again with a different date.
        // The initial reminder flag should still be false.
        CreateOrUpdateReminderAction::create()
            ->execute(
                $this->makeReminderData(Carbon::now()->addWeek()),
                $user,
                $reminder
            );

        // Initial reminder flag should still be false
        $this->assertFalse($reminder->hasInitialReminderRun());
    }

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
    public function can_change_frequency_to_none()
    {
        $reminder = $this->createReminderFor($user = $this->signIn());
        $reminder->setRecurrenceFrequency('monthly');
        $this->assertTrue($reminder->isRecurring());

        CreateOrUpdateReminderAction::create()
            ->execute($this->makeReminderData(), $user, $reminder);

        $this->assertFalse($reminder->isRecurring());
        $this->assertEquals("No recurrence", $reminder->frequency);
    }

    /** @test */
    public function updating_frequency_when_initial_has_not_run_will_not_affect_next_run_date()
    {
        $date = Carbon::now()->addMonth();
        $reminder = $this->createReminderFor($user = $this->signIn(), 1, $date);
        $this->assertFalse($reminder->hasInitialReminderRun());

        $updatedData = $this->makeReminderData($date);
        $updatedData->frequency = "daily";

        CreateOrUpdateReminderAction::create()->execute($updatedData, $user, $reminder);

        $this->assertEquals($date->format('d/m/Y H:i'), $reminder->fresh()->next_run->format('d/m/Y H:i'));
    }

    /** @test */
    public function updating_frequency_when_initial_has_run_will_not_affect_next_run_date()
    {
        $date = Carbon::now('Europe/London')->addYear();
        $reminder = $this->createReminderFor($user = $this->signIn(), 1, $date);
        $reminder->markInitialReminderComplete();
        $this->assertTrue($reminder->hasInitialReminderRun());

        // Update
        $updatedData = $this->makeReminderData($date);
        $updatedData->frequency = "monthly";
        CreateOrUpdateReminderAction::create()->execute($updatedData, $user, $reminder);

        $this->assertEquals($date->format('d/m/Y H:i'), $reminder->fresh()->next_run->format('d/m/Y H:i'));
    }

    /** @test */
    public function changing_from_a_valid_frequency_to_none_doesnt_affect_next_run_date()
    {
        $date = Carbon::now('Europe/London')->addYear();
        $reminder = $this->createReminderFor($user = $this->signIn(), 1, $date);
        $reminder->markInitialReminderComplete();
        $reminder->setRecurrenceFrequency('yearly');
        $this->assertTrue($reminder->hasInitialReminderRun());
        $this->assertTrue($reminder->isRecurring());

        // Update
        $updatedData = $this->makeReminderData($date);
        $updatedData->frequency = "none";
        CreateOrUpdateReminderAction::create()->execute($updatedData, $user, $reminder);
        $this->assertFalse($reminder->isRecurring());
        $this->assertEquals($date->format('d/m/Y H:i'), $reminder->fresh()->next_run->format('d/m/Y H:i'));
    }

    /**
     * @todo
     * Tests for updating the frequency.
     */

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
