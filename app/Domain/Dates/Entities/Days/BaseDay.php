<?php

namespace App\Domain\Dates\Entities\Days;

class BaseDay
{
    /**
     * @var integer - Day number
     */
    protected $day;

    /**
     * @var string - Day name
     */
    protected $name;

    public function __invoke()
    {
        return $this->getDayNumber();
    }

    public function getDayNumber()
    {
        return $this->day;
    }

    public function getDayName()
    {
        return $this->name;
    }
}
