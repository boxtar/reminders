<?php

declare(strict_types=1);

namespace App\Domain\Reminders;

use App\Domain\Dates\DatesSupport;
use Carbon\Carbon;

class ReminderData
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Extracts relevant data from input for creating a new Reminder.
     * 
     * @param array $input
     */
    public function __construct(array $input)
    {
        $this->data['body'] = $input['body'] ?? '';
        $this->data['date'] = $input['date'] ?? '';
        $this->data['month'] = $input['month'] ?? '';
        $this->data['year'] = $input['year'] ?? '';
        $this->data['time'] = $input['time'] ?? '';
        $this->data['frequency'] = $input['frequency'] ?? '';
        $this->data['channels'] = $input['channels'] ?? [];

        // These won't be part of API request as they are built in domain
        $this->data['initial_reminder_run'] = false;
        $this->data['expression'] = '';
        $this->data['is_recurring'] = false;
        $this->data['recurrence_expression'] = '';

        // Extract hour and minute from the time value
        $this->updateTime($this->time);

        // Build a Carbon date
        $date = Carbon::create($this->year, $this->month + 1, $this->date, $this->hour, $this->minute, 0);

        // Figure the day of the week out
        $this->data['day'] = $date->dayOfWeek;

        // Set the next_run field to the carbon instance
        $this->data['next_run'] = $date;
    }

    /**
     * Quick n Dirty factory for building an instance
     * 
     * @param array $input
     */
    public static function createFromArray(array $data)
    {
        return new self($data);
    }

    /**
     * @param string
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            $this->data[$name] = $value;

            if ($name == 'time') $this->updateTime($value);
        }
    }

    protected function updateTime($time)
    {
        // Must access directly or we'll end up re-calling this function from __get
        $this->data['time'] = $time;

        // Default to midnight if no time passed in
        if (!$time) $this->data['time'] = '00:00';

        // Extract hour and minute from the time value
        [$hour, $minute] = DatesSupport::extractTimeValues($this->time);

        // Accessing directly as it is not guaranteed that 'hour' and 'minute' keys
        // will exist on the data array when this is invoked.
        $this->data['hour'] = $hour;
        $this->data['minute'] = $minute;
    }

    public function toArray()
    {
        return $this->data;
    }
}
