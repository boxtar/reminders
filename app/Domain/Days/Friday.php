<?php

namespace App\Domain\Days;

class Friday {
    /**
     * @var integer - Day number
     */
    protected $day = 5;

    /**
     * @var string - Day name
     */
    protected $name = "friday";

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