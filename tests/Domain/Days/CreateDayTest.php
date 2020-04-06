<?php

declare(strict_types=1);

namespace Tests\Domain\Days;

use App\Domain\Days\CreateDay;
use App\Domain\Days\Friday;
use App\Domain\Days\Monday;
use App\Domain\Days\Saturday;
use App\Domain\Days\Sunday;
use App\Domain\Days\Thursday;
use App\Domain\Days\Tuesday;
use App\Domain\Days\Wednesday;
use Tests\TestCase;

class CreateDayTest extends TestCase
{
    /** @test */
    public function it_returns_correct_day_from_day_number()
    {
        $this->assertInstanceOf(Sunday::class, CreateDay::fromDayNumber(0));
        $this->assertInstanceOf(Monday::class, CreateDay::fromDayNumber(1));
        $this->assertInstanceOf(Tuesday::class, CreateDay::fromDayNumber(2));
        $this->assertInstanceOf(Wednesday::class, CreateDay::fromDayNumber(3));
        $this->assertInstanceOf(Thursday::class, CreateDay::fromDayNumber(4));
        $this->assertInstanceOf(Friday::class, CreateDay::fromDayNumber(5));
        $this->assertInstanceOf(Saturday::class, CreateDay::fromDayNumber(6));
    }

    /** @test */
    public function it_throws_an_exception_on_invalid_day_number()
    {
        $this->expectException(\Exception::class);
        CreateDay::fromDayNumber(8);
    }
}
