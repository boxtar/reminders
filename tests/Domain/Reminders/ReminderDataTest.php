<?php

namespace Tests\Domain\Reminders;

use App\Domain\Reminders\ReminderData;
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
}
