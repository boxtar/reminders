<?php

declare(strict_types=1);

namespace App\Domain\Reminders\Rules;

use App\Domain\Reminders\ReminderData;

abstract class BaseRule
{
    /**
     * @var ReminderData - Data to be validated
     */
    protected $data;

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
     * Must be implemented by sub-classes.
     * Remember to return $this.
     * 
     * @return BaseRule
     */
    abstract public function validate(): BaseRule;

    /**
     * Indicates if validation passed
     * 
     * @return bool
     */
    public function passes(): bool
    {
        return $this->_passes;
    }

    /**
     * Indicates if validation failed
     * 
     * @return bool
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Returns all errors
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }
}
