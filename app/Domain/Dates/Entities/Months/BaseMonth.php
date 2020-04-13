<?php

namespace App\Domain\Dates\Entities\Months;

abstract class BaseMonth
{
    /**
     * @var integer - Month number
     */
    protected $day;

    /**
     * @var string - Month name
     */
    protected $name;

    public function __invoke()
    {
        return $this->getMonthNumber();
    }

    public function getMonthNumber()
    {
        return $this->day;
    }

    public function getMonthName()
    {
        return $this->name;
    }
}