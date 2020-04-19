<?php

declare(strict_types=1);

namespace Tests\Domain\Days;

use App\Domain\Dates\Entities\Days\Saturday;
use Tests\TestCase;

class SaturdayTest extends TestCase
{
    protected $className = Saturday::class;

    protected $dayNumber = 6;
    
    protected $dayName = "saturday";

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