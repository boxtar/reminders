<?php

namespace App\Domain\Days;

class Wednesday {
    /**
     * @var integer - Day number
     */
    protected $day = 3;

    /**
     * @var string - Day name
     */
    protected $name = "wednesday";

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