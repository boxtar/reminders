<?php

namespace App\Domain\Days;

class Monday {
    /**
     * @var integer - Day number
     */
    protected $day = 1;

    /**
     * @var string - Day name
     */
    protected $name = "monday";

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