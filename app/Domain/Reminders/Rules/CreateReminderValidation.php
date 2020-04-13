<?php

declare(strict_types=1);

namespace App\Domain\Reminders\Rules;

use App\Domain\Dates\DatesSupport;
use App\Domain\Reminders\ReminderData;

class CreateReminderValidation
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
        if (!DatesSupport::isValidDate($this->_data->date)) {
            $this->_errors['date'] = "Invalid Date: {$this->_data->date}";
        }

        // Month
        if (!DatesSupport::isValidMonth($this->_data->month)) {
            $this->_errors['month'] = "Invalid Month: {$this->_data->month}";
        }

        // Time
        if (!DatesSupport::isValidTime($this->_data->time)) {
            $this->_errors['time'] = "Invalid Time: {$this->_data->time}";
        }

        if (!count($this->_errors)) {
            $this->_passes = true;
        }

        return $this;
    }

    /**
     * Indicates if validation passed
     * 
     * @return bool
     */
    public function passes()
    {
        return $this->_passes;
    }

    /**
     * Indicates if validation failed
     * 
     * @return bool
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * Returns all errors
     * 
     * @return array
     */
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
}
