<?php

declare(strict_types=1);

namespace Tests\Domain\Dates\Entities\Months;

use App\Domain\Dates\Entities\Months\March;
use Tests\TestCase;

class MarchTest extends TestCase
{
    protected $className = March::class;

    protected $monthNumber = 2;
    
    protected $monthName = "march";

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