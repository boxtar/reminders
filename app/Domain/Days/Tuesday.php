<?php

namespace App\Domain\Days;

class Tuesday {
    /**
     * @var integer - Day number
     */
    protected $day = 2;

    /**
     * @var string - Day name
     */
    protected $name = "tuesday";

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