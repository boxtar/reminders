<?php

namespace Tests\Domain\Recurrence;

use DateTimeZone;
use Carbon\Carbon;
use Tests\TestCase;
use App\Domain\Dates\DatesSupport;
use App\Domain\Reminders\ReminderData;
use App\Domain\Recurrences\RecurrenceBuilder;

class RecurrenceBuilderTest extends TestCase
{
    /** @test */
    public function builder_returns_false_if_frequency_unrecognised()
    {
        $reminder = $this->makeReminderData();
        $reminder->frequency = "nonExistentFrequency";
        $builder = new RecurrenceBuilder($reminder);
        $this->assertNull($builder->build());
    }
    
    /** @test */
    public function daily_frequency_returns_correct_expression()
    {
        $reminder = $this->makeReminderData();
        $reminder->frequency = "daily";
        $builder = new RecurrenceBuilder($reminder);
        $this->assertEquals("{$reminder->minute} {$reminder->hour} * * *", $builder->build());
    }

    /** @test */
    public function weekly_frequency_returns_correct_expression()
    {
        $reminder = $this->makeReminderData();
        $reminder->frequency = "weekly";
        $builder = new RecurrenceBuilder($reminder, 'weekly');
        $this->assertEquals("{$reminder->minute} {$reminder->hour} * * {$reminder->day}", $builder->build());
    }

    /** @test */
    public function monthly_frequency_returns_correct_expression()
    {
        $reminder = $this->makeReminderData();
        $reminder->frequency = "monthly";
        $reminder->date = 1;
        $builder = new RecurrenceBuilder($reminder);
        $this->assertEquals("{$reminder->minute} {$reminder->hour} 1 * *", $builder->build());

        // Test with capped date
        $reminder->date = 31;
        $builder = new RecurrenceBuilder($reminder);
        // Should be capped at 28
        $this->assertEquals("{$reminder->minute} {$reminder->hour} 28 * *", $builder->build());
    }

    /** @test */
    public function quarterly_frequency_returns_correct_expression()
    {
        $reminder = $this->makeReminderData();
        $reminder->frequency = "quarterly";

        $reminder->date = 31; // 31st
        $reminder->month = 0; // January
        $reminder->year = 2020;
        $builder = new RecurrenceBuilder($reminder);
        // capped at 30th because April
        $this->assertEquals("{$reminder->minute} {$reminder->hour} 30 1,4,7,10 *", $builder->build());

        // Test with February
        $reminder->month = 1;
        $builder = new RecurrenceBuilder($reminder);
        // capped at 28th because February
        $this->assertEquals("{$reminder->minute} {$reminder->hour} 28 2,5,8,11 *", $builder->build());

        // Test with a month that will cross over into the next year
        $reminder->month = 10; // November
        $builder = new RecurrenceBuilder($reminder);
        // Date should be capped at 28 because February is one of the quarterly months
        $this->assertEquals("{$reminder->minute} {$reminder->hour} 28 11,2,5,8 *", $builder->build());

        // Another test for sanity
        $reminder->month = 5; // June
        $reminder->date = 12;
        $builder = new RecurrenceBuilder($reminder);
        $this->assertEquals("{$reminder->minute} {$reminder->hour} 12 6,9,12,3 *", $builder->build());
    }

    /** @test */
    public function yearly_frequency_returns_correct_expression()
    {
        $reminder = $this->makeReminderData();
        $reminder->frequency = "yearly";
        $builder = new RecurrenceBuilder($reminder);
        // ReminderData month is zero-based
        $month = $reminder->month + 1;
        $this->assertEquals("{$reminder->minute} {$reminder->hour} {$reminder->date} {$month} *", $builder->build());
    }
}
