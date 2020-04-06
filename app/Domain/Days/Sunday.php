<?php

namespace App\Domain\Days;

class Sunday {
    /**
     * @var integer - Day number
     */
    protected $day = 0;

    /**
     * @var string - Day name
     */
    protected $name = "sunday";

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