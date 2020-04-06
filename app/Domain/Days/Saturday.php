<?php

namespace App\Domain\Days;

class Saturday {
    /**
     * @var integer - Day number
     */
    protected $day = 6;

    /**
     * @var string - Day name
     */
    protected $name = "saturday";

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