<?php

declare(strict_types=1);

namespace Tests\Domain\Dates\Entities\Days;

use App\Domain\Dates\Entities\Days\Friday;
use Tests\TestCase;

class FridayTest extends TestCase
{
    protected $className = Friday::class;

    protected $dayNumber = 5;
    
    protected $dayName = "friday";

    /** @test */
    public function returns_correct_day_number()
    {
        $day = new $this->className;

        $this->assertEquals($this->dayNumber, $day->getDayNumber());
    }

    /** @test */
    public function returns_correct_day_number_when_invoked()
    {
        $day = new $this->className;

        $this->assertEquals($this->dayNumber, $day());
    }

    /** @test */
    public function returns_correct_day_name()
    {
        $day = new $this->className;

        $this->assertEquals($this->dayName, $day->getDayName());
    }
}