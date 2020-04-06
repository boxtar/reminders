<?php

declare(strict_types=1);

namespace Tests\Domain\Reminders;

use App\Domain\Reminders\ReminderData;
use Tests\TestCase;

class CreateTest extends TestCase
{
    /** @test */
    public function create_request_extracts_required_data_from_array_input()
    {
        $input = [
            'body' => 'Test Reminder',
            'date' => 1,
            'month' => 0,
            'year' => 2020,
            'time' => "12:00",
            'frequency' => "none",
        ];

        $request = new ReminderData($input);

        // Body
        $this->assertEquals($input['body'], $request->body);
        // Date
        $this->assertEquals($input['date'], $request->date);
        // Month
        $this->assertTrue(is_int($request->month), 'month does not appear to be an int');
        $this->assertEquals($input['month'], $request->month);
        // Year
        $this->assertEquals($input['year'], $request->year);
        // Time
        $this->assertEquals($input['time'], $request->time);
        // Frequency
        $this->assertEquals($input['frequency'], $request->frequency);
        // Channels
        $this->assertContains('mail', $request->channels);
    }
}
