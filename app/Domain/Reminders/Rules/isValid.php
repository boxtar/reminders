<?php

declare(strict_types=1);

namespace App\Domain\Reminders\Rules;

use App\Domain\Reminders\ReminderData;

class isValid
{
    /**
     * @var ReminderData - Data to be validated
     */
    protected $_data;

    /**
     * @var bool - Indicates if validation has passed
     */
    protected $_passes = false;

    /**
     * @var array - Validation errors. (Should be a class)
     */
    protected $_errors = [];

    public function __construct(ReminderData $data)
    {
        $this->_data = $data;
    }
    /**
     * Validate the data
     */
    public function validate()
    {
        // Body
        if (!$this->_data->body) {
            $this->_errors['body'] = 'You must provide a body';
        }

        // Date
        if (!$this->isValidDate($this->_data->date)) {
            $this->_errors['date'] = "Invalid Date: {$this->_data->date}";
        }

        // Month
        if (!$this->isValidMonth($this->_data->month)) {
            $this->_errors['month'] = "Invalid Month: {$this->_data->month}";
        }

        // Time
        if (!$this::isValidTime($this->_data->time)) {
            $this->_errors['time'] = "Invalid Time: {$this->_data->time}";
        }

        if (!count($this->_errors)) {
            $this->_passes = true;
        }

        return $this;
    }

    public function passes()
    {
        return $this->_passes;
    }

    public function fails()
    {
        return !$this->passes();
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getError($key)
    {
        $errors = $this->getErrors();
        if (count($errors) && array_key_exists($key, $errors)) {
            return $errors[$key];
        }
        return false;
    }

    /**
     * List on valid months.
     */
    protected function months()
    {
        return [
            0 => 'January',
            1 => 'February',
            2 => 'March',
            3 => 'April',
            4 => 'May',
            5 => 'June',
            6 => 'July',
            7 => 'August',
            8 => 'September',
            9 => 'October',
            10 => 'November',
            11 => 'December',
        ];
    }

    /**
     * Bare minimum date validation. Simply validates that the provided
     * date is at least 1 or at max 31. This does not constrain the day
     * based on a given month (e.g. 30 for November)
     * 
     * @param string|int $date - The date to be checked
     */
    protected function isValidDate($date)
    {
        return $date > 0 && $date < 32;
    }

    protected function isValidTime($time)
    {
        // Time will be in format "HH:MM". Split into Hour and Minute parts using colon.
        // Then map over the resulting array and cast each string part to int.
        [$hour, $minute] = array_map(function ($part) {
            return (int) $part;
        }, explode(":", $time));
        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) return false;
        return true;
    }

    /**
     * Returns true if provide month is valid. The valid month numbers and
     * their corresponding month are defined in the static months method.
     * 
     * @param string|int $month - The number representing the month.
     */
    protected function isValidMonth($month)
    {
        return isset($this->months()[(int) $month]);
    }
}
