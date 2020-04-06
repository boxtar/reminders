<?php

namespace App\Domain\Days;

class Thursday {
    /**
     * @var integer - Day number
     */
    protected $day = 4;

    /**
     * @var string - Day name
     */
    protected $name = "thursday";

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