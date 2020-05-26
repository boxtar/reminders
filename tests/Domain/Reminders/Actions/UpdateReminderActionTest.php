<?php

namespace Tests\Domain\Reminders\Actions;

use App\Domain\Dates\DatesSupport;
use App\Domain\Reminders\Actions\CreateOrUpdateReminderAction;
use App\Domain\Reminders\Models\Reminder;
use App\Domain\Reminders\ReminderData;
use Tests\TestCase;

class CreateOrUpdateReminderActionTest extends TestCase
{
    /** @test */
    public function can_create_from_static_factory()
    {
        $this->assertInstanceOf(CreateOrUpdateReminderAction::class, CreateOrUpdateReminderAction::create());
    }

    public function sets_errors_and_status_when_validation_fails()
    {
        $reminderData = $this->makeReminderData();
        $reminder = $this->makeReminder();
        $reminderData->body = "";
        $response = CreateOrUpdateReminderAction::create()->execute($reminderData, $user, $reminder);

        $this->assertEquals(400, $response->getStatus());
        $this->assertCount(1, $response->getErrors());
        $this->assertArrayHasKey('body', $response->getErrors());
    }

    public function sets_status_and_data_on_success()
    {
        $this->markTestIncomplete("Need to setup testing database to truly test this logic");
        
        // Original reminder (would be retrieved from persistence).
        $reminderData = $this->makeReminderData();

        // New data to be applied as update to original reminder.
        $newReminderData = array_merge($reminderData->toArray(), ['body' => 'Updated Reminder']);
        $newReminderData = ReminderData::createFromArray($newReminderData);

        /** @var Reminder $reminder */
        $reminder = $this->buildMockReminder($reminderData, $newReminderData);

        // Execute updating body. However, it's all mocked...
        // Is this really proving anything?...
        $response = CreateOrUpdateReminderAction::create()->execute($reminderData, $reminder);

        $this->assertEquals("Updated Reminder", $response->getData()['body']);
    }

    public function can_update_body_without_affecting_other_attribs()
    {
        $this->markTestIncomplete("Need to setup testing database to truly test this logic");

        // Original reminder (would be retrieved from persistence).
        $reminderData = $this->makeReminderData();

        // New data to be applied as update to original reminder.
        $newReminderData = array_merge($reminderData->toArray(), ['body' => 'Updated Reminder']);
        $newReminderData = ReminderData::createFromArray($newReminderData);

        /** @var Reminder $reminder */
        $reminder = $this->buildMockReminder($reminderData, $newReminderData);

        // Now update the body
        $reminderData->body = "Updated Reminder";
        $response = CreateOrUpdateReminderAction::create()->execute($reminderData, $reminder);

        // Build the month name
        $monthName = ucfirst(DatesSupport::makeMonth($reminderData->month)->getMonthName());

        $this->assertEquals("Updated Reminder", $response->getData()['body']);
        $this->assertEquals(DatesSupport::makeOrdinal($reminderData->date), $response->getData()['date']);
        $this->assertEquals($monthName, $response->getData()['month']);
        $this->assertEquals($reminderData->year, $response->getData()['year']);
        $this->assertEquals($reminderData->hour, $response->getData()['hour']);
        $this->assertEquals($reminderData->minute, $response->getData()['minute']);
        // assert initial_reminder_run, is_recurring, expression and recurrence_expression are also
        // the same as they were.
    }

    // Test can update reminder date. It should update 'expression' and
    // 'initial_reminder_run' accordingly.

    // Test can update reminder frequency. It should update 'recurrence_expression' and
    // 'is_recurring' accordingly

    /**
     * 1st param is the original Reminder data.
     * 2nd param is the new Reminder data to be applied in the update.
     * 
     * @param ReminderData $data
     * @param ReminderData $newData
     */
    protected function buildMockReminder(ReminderData $data, ReminderData $newData)
    {
        /** @var Reminder $reminder */
        $reminder = $this->getMockBuilder(Reminder::class)
            ->setMethods(['update', 'fresh', 'save'])
            ->getMock();

        // Set up the expectation for the save() method
        // to be called only once and return true.
        $reminder->expects($this->once())
            ->method('update')
            ->willReturn(true);

        // Set up the expectation for the fresh() method
        // to be called only once and return the updated
        // Reminder model.
        // $updatedReminder = $reminder->replicate();
        // $this->setReminderAttributes($updatedReminder, $newData);
        // $reminder->expects($this->once())
        //     ->method('fresh')
        //     ->willReturn($updatedReminder);

        // Build out the initial reminder state
        return $this->setReminderAttributes($reminder, $data);
    }

    protected function setReminderAttributes(Reminder &$reminder, ReminderData $data)
    {
        $reminder->body = $data->body;
        $reminder->date = $data->date;
        $reminder->month = $data->month;
        $reminder->year = $data->year;
        $reminder->hour = $data->hour;
        $reminder->minute = $data->minute;
        return $reminder;
    }
}
