<?php

namespace App\Domain\Reminders;

use App\Domain\Dates\DatesSupport;
use App\Domain\Reminders\ReminderData;

class ReminderExpressionBuilder
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
            'month' => $reminder->month + 1, // month is zero-based
            'year' => $reminder->year,
        ];
    }

    public static function createFromReminderData(ReminderData $data)
    {
        return new self($data);
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
