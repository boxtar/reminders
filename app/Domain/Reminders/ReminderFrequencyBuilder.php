<?php

namespace App\Domain\Reminders;

use App\Domain\Dates\DatesSupport;
use App\Domain\Reminders\ReminderData;

class ReminderFrequencyBuilder
{

    /**
     * @var ReminderData
     */
    protected $reminderData;

    /**
     * @param ReminderData
     * @param string
     */
    public function __construct(ReminderData $reminder)
    {
        [$hour, $minute] = DatesSupport::extractTimeValues($reminder->time);

        $this->reminderData = (object) [
            'minute' => $minute,
            'hour' => $hour,
            'day' => $reminder->day,
            'date' => $reminder->date,
            'month' => $reminder->month + 1, // Income month is zero-based
            'year' => $reminder->year,
        ];
    }

    /**
     * Returns the cron expression
     */
    public function build()
    {
        return implode(" ", [
            $this->reminderData->minute,
            $this->reminderData->hour,
            $this->reminderData->date,
            $this->reminderData->month,
            "*"
        ]);
    }
}
