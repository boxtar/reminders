<?php

namespace App\Domain\Recurrences;

use App\Domain\Dates\DatesSupport;
use App\Domain\Reminders\ReminderData;

class RecurrenceBuilder
{

    /**
     * @var ReminderData
     */
    protected $reminderData;

    /**
     * @var string
     */
    protected $recurrenceFrequency;

    /**
     * @param ReminderData
     * @param string
     */
    public function __construct(ReminderData $reminder, string $frequency)
    {
        $this->reminderData = (object) [
            'minute' => $reminder->minute,
            'hour' => $reminder->hour,
            'day' => $reminder->day,
            'date' => $reminder->date,
            'month' => $reminder->month + 1, // Income month is zero-based
            'year' => $reminder->year,
        ];

        $this->recurrenceFrequency = $frequency;
    }

    /**
     * Builds and returns a cron expression
     * 
     * @param ReminderData
     * @param string
     * @return string
     */
    public function build()
    {
        if (method_exists($this, $this->recurrenceFrequency)) {
            return $this->{$this->recurrenceFrequency}();
        }
        return null;
    }

    protected function daily()
    {
        return implode(" ", [
            $this->reminderData->minute,
            $this->reminderData->hour,
            "*",
            "*",
            "*",
        ]);
    }

    protected function weekly()
    {
        return implode(" ", [
            $this->reminderData->minute,
            $this->reminderData->hour,
            "*",
            "*",
            $this->reminderData->day,
        ]);
    }

    protected function monthly()
    {
        // If this is going to recur every month then cap to the date
        // with the lowest number of days (28 - no leap years).
        $date = min(28, $this->reminderData->date);
        return implode(" ", [
            $this->reminderData->minute,
            $this->reminderData->hour,
            $date,
            "*",
            "*"
        ]);
    }

    protected function quarterly()
    {
        $date = $this->reminderData->date;

        // months to recur on
        $recurringMonths = [];
        for ($i = 0; $i < 4; $i++) {
            $recurringMonths[] = $this->reminderData->month + (3 * $i);
            // Cap date if it exceeds the max for one of the months to recur on
            $date = DatesSupport::isDateValidForMonth($date, $recurringMonths[$i]) ? $date : DatesSupport::getLastDateForMonth($recurringMonths[$i]);
        }

        return implode(" ", [
            $this->reminderData->minute,
            $this->reminderData->hour,
            $date,
            implode(",", $recurringMonths),
            "*"
        ]);
    }

    protected function yearly()
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
