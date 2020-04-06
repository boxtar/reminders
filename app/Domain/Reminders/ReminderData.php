<?php

declare(strict_types=1);

namespace App\Domain\Reminders;

use App\Domain\Dates\DatesSupport;

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
        $this->data['expression'] = $input['expression'] ?? '';
        $this->data['frequency'] = $input['frequency'] ?? '';

        // This won't be part of API request. It's built on backend.
        $this->data['recurrence_expression'] = '';

        // Hardcoded to all available channels for now
        $this->data['channels'] = ["telegram", "mail", "sms"];

        // Extract hour and minute from the time value
        [$hour, $minute] = DatesSupport::extractTimeValues($this->time);
        $this->data['hour'] = $hour;
        $this->data['minute'] = $minute;

        // Figure the day of the week out
        $this->data['day'] = date('w', mktime($this->hour, $this->minute, 0, $this->month + 1, $this->date, $this->year));
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
        $this->data[$name] = $value;
        // if (array_key_exists($name, $this->data)) {
        //     $this->data[$name] = $value;
        // }
    }

    public function toArray()
    {
        return $this->data;
    }
}
