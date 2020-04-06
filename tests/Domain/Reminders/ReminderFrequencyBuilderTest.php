<?php

namespace Tests\Domain\Reminders;

use App\Domain\Dates\DatesSupport;
use App\Domain\Reminders\ReminderData;
use App\Domain\Reminders\ReminderFrequencyBuilder;
use Tests\TestCase;

class ReminderFrequencyBuilderTest extends TestCase
{
    /** @test */
    public function it_returns_the_correct_cron_expression()
    {
        $reminder = $this->getTestReminderData();
        $builder = new ReminderFrequencyBuilder($reminder);
        [$hour, $minute] = DatesSupport::extractTimeValues($reminder->time);
        // ReminderData month is zero-based
        $month = $reminder->month + 1;
        $this->assertEquals("{$minute} {$hour} {$reminder->date} {$month} *", $builder->build());
    }

    protected function getTestReminderData()
    {
        $date = \Carbon\Carbon::now(new \DateTimeZone('Europe/London'));
        return new ReminderData([
            'body' => 'Test Reminder',
            // 'day' => $date->dayOfWeek,
            'date' => $date->day,
            // Carbon is not zero based for months, JS is so this accurately reflects
            // how ReminderData will be instantiated from the API request.
            'month' => $date->month - 1,
            'year' => $date->year,
            'time' => "{$date->hour}:{$date->minute}",
            'frequency' => "none",
        ]);
    }
}
