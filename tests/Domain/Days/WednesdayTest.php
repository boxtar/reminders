<?php

declare(strict_types=1);

namespace Tests\Domain\Days;

use App\Domain\Days\Wednesday;
use Tests\TestCase;

class WednesdayTest extends TestCase
{
    protected $className = Wednesday::class;

    protected $dayNumber = 3;
    
    protected $dayName = "wednesday";

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