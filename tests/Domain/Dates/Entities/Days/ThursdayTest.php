<?php

declare(strict_types=1);

namespace Tests\Domain\Days;

use App\Domain\Dates\Entities\Days\Thursday;
use Tests\TestCase;

class ThursdayTest extends TestCase
{
    protected $className = Thursday::class;

    protected $dayNumber = 4;
    
    protected $dayName = "thursday";

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