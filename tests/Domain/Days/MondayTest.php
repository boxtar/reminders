<?php

declare(strict_types=1);

namespace Tests\Domain\Days;

use Tests\TestCase;
use App\Domain\Days\Monday;

class MondayTest extends TestCase
{
    protected $className = Monday::class;

    protected $dayNumber = 1;
    
    protected $dayName = "monday";

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