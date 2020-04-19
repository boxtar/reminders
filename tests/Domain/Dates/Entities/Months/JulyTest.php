<?php

declare(strict_types=1);

namespace Tests\Domain\Dates\Entities\Months;

use App\Domain\Dates\Entities\Months\July;
use Tests\TestCase;

class JulyTest extends TestCase
{
    protected $className = July::class;

    protected $monthNumber = 6;
    
    protected $monthName = "july";

    /** @test */
    public function returns_correct_month_number()
    {
        $month = new $this->className;

        $this->assertEquals($this->monthNumber, $month->getMonthNumber());
    }

    /** @test */
    public function returns_correct_month_number_when_invoked()
    {
        $month = new $this->className;

        $this->assertEquals($this->monthNumber, $month());
    }

    /** @test */
    public function returns_correct_month_name()
    {
        $month = new $this->className;

        $this->assertEquals($this->monthName, $month->getMonthName());
    }
}