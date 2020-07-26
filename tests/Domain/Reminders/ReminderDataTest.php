<?php

namespace Tests\Domain\Reminders;

use App\Domain\Reminders\ReminderData;
use Carbon\Carbon;
use Tests\TestCase;

class ReminderDataTest extends TestCase
{

    /** @test */
    public function can_create_from_array()
    {
        $data = ReminderData::createFromArray([
            'body' => 'Test Reminder',
            'date' => 5, // 5th
            'month' => 3, // April
            'year' => 2020,
            'time' => "12:00", // Noon
        ]);

        $this->assertEquals('Test Reminder', $data->body);
        $this->assertEquals(2020, $data->year);
        $this->assertEquals("12:00", $data->time);
    }

    /** @test */
    public function it_calculates_the_correct_day_of_the_week()
    {
        $data = new ReminderData([
            'body' => 'Test Reminder',
            'date' => 5, // 5th
            'month' => 3, // April
            'year' => 2020,
            'time' => "12:00", // Noon
        ]);

        // Assert day is 0 (Sunday)
        $this->assertEquals(0, $data->day);
    }

    /** @test */
    public function it_extracts_the_hour_and_minute_from_the_time()
    {
        $data = new ReminderData([
            'body' => 'Test Reminder',
            'date' => 5, // 5th
            'month' => 3, // April
            'year' => 2020,
            'time' => "12:00", // Noon
        ]);

        $this->assertEquals(12, $data->hour);
        $this->assertEquals(0, $data->minute);
    }

    /** @test */
    public function sets_hour_and_minute_when_time_is_updated()
    {
        $data = $this->makeReminderData();
        $hour = $data->hour + 1; // Change by 1 hour
        $minute = $data->minute + 1; // Change by 1 minute
        $data->time = "{$hour}:{$minute}";
        $this->assertEquals("{$hour}:{$minute}", $data->time);
        $this->assertEquals($hour, $data->hour);
        $this->assertEquals($minute, $data->minute);
    }

    /** @test */
    public function can_be_cast_to_an_array()
    {
        $data = new ReminderData([
            'body' => 'Test Reminder',
            'date' => 5, // 5th
            'month' => 3, // April
            'year' => 2020,
            'time' => "12:00", // Noon
            'frequency' => "none"
        ]);
        $this->assertArrayHasKey('body', $data->toArray());
    }

    /** @test */
    public function can_set_is_recurring_flag()
    {
        $reminder = $this->makeReminderData();
        $reminder->is_recurring = true;
        $this->assertTrue($reminder->is_recurring);
    }

    /** @test */
    public function can_set_desired_channels()
    {
        // Set on instantiation
        $reminderData = ReminderData::createFromArray([
            'body' => 'Test Reminder',
            'date' => 5, // 5th
            'month' => 3, // April
            'year' => 2020,
            'time' => "12:00", // Noon
            'channels' => ['email'], // Only email channel
        ]);
        $this->assertCount(1, $reminderData->channels);
        $this->assertEquals('email', $reminderData->channels[0]);

        // Set after instantiation 
        $reminderData->channels = ['email', 'sms'];
        $this->assertCount(2, $reminderData->channels);
        $this->assertEquals('sms', $reminderData->channels[1]);
    }

    /** @test */
    public function it_sets_the_next_run_date()
    {
        $dt = Carbon::now('Europe/London');
        $reminderData = $this->makeReminderData($dt);
        $this->assertEquals($dt->day, $reminderData->next_run->day);
        $this->assertEquals($dt->month, $reminderData->next_run->month);
        $this->assertEquals($dt->year, $reminderData->next_run->year);
        $this->assertEquals($dt->hour, $reminderData->next_run->hour);
        $this->assertEquals($dt->minute, $reminderData->next_run->minute);
    }
}
