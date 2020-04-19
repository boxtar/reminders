<?php

declare(strict_types=1);

namespace Tests\Domain\Dates\Entities\Months;

use App\Domain\Dates\Entities\Months\April;
use Tests\TestCase;

class AprilTest extends TestCase
{
    protected $className = April::class;

    protected $monthNumber = 3;
    
    protected $monthName = "april";

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