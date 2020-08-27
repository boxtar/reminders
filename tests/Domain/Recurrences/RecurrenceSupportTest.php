<?php

namespace Tests\Domain\Recurrence;

use App\Domain\Recurrences\Exceptions\InvalidRecurrenceException;
use App\Domain\Recurrences\RecurrencesSupport;
use Carbon\Carbon;
use Tests\TestCase;

class RecurrenceSupportTest extends TestCase
{
    /** @test */
    public function unknown_recurrence_throws_exception()
    {
        $this->expectException(InvalidRecurrenceException::class);
        $this->expectExceptionMessage('invalid_recurrence is not a valid recurrence');
        (new RecurrencesSupport())->forwardDateByRecurrence(Carbon::now(), 'invalid_recurrence');
    }

    /** @test */
    public function hourly_recurrence_forwards_given_date_by_one_hour()
    {
        $recurrence = 'hourly';
        $dateBefore = Carbon::create(2020, 1, 1, 23, 0, 0);
        $dateAfter = Carbon::create(2020, 1, 2);

        // Assert date has been forwarded correctly
        $this->assertDatesEqual(
            $dateAfter,
            (new RecurrencesSupport())->forwardDateByRecurrence($dateBefore, $recurrence)
        );

        // Also assert that original date has not been altered
        $this->assertDatesEqual($dateAfter->subHour(), $dateBefore);
    }

    /** @test */
    public function daily_recurrence_forwards_given_date_by_one_day()
    {
        $recurrence = 'daily';
        $dateBefore = Carbon::create(2020, 1, 1);
        $dateAfter = Carbon::create(2020, 1, 2);

        // Assert date has been forwarded correctly
        $this->assertDatesEqual(
            $dateAfter,
            (new RecurrencesSupport())->forwardDateByRecurrence($dateBefore, $recurrence)
        );

        // Also assert that original date has not been altered
        $this->assertDatesEqual($dateAfter->subDay(), $dateBefore);
    }

    /** @test */
    public function weekdays_recurrence_forwards_given_date_by_one_day_but_skips_over_weekends()
    {
        $recurrence = 'weekdays';
        // Thursday, 27th Aug
        $dateBefore = Carbon::create(2020, 8, 27);
        // Expect to be updated to Friday, 28th Aug
        $dateAfter = Carbon::create(2020, 8, 28);

        // Assert date has been forwarded correctly
        $this->assertDatesEqual(
            $dateAfter,
            (new RecurrencesSupport())->forwardDateByRecurrence($dateBefore, $recurrence)
        );

        // Also assert that original date has not been altered
        $this->assertDatesEqual($dateAfter->subDay(), $dateBefore);

        // Friday, 28th Aug
        $dateBefore = Carbon::create(2020, 8, 28);
        // Expect to be updated to Monday, 31st Aug (skip the weekend)
        $dateAfter = Carbon::create(2020, 8, 31);

        // Assert date has been forwarded correctly
        $this->assertDatesEqual(
            $dateAfter,
            (new RecurrencesSupport())->forwardDateByRecurrence($dateBefore->addDay(), $recurrence)
        );
    }

    /** @test */
    public function weekly_recurrence_forwards_given_date_by_one_week()
    {
        $recurrence = 'weekly';
        $dateBefore = Carbon::create(2020, 1, 1);
        $dateAfter = Carbon::create(2020, 1, 8);

        // Assert date has been forwarded correctly
        $this->assertDatesEqual(
            $dateAfter,
            (new RecurrencesSupport())->forwardDateByRecurrence($dateBefore, $recurrence)
        );

        // Also assert that original date has not been altered
        $this->assertDatesEqual($dateAfter->subWeek(), $dateBefore);
    }

    /** @test */
    public function monthly_recurrence_forwards_given_date_by_one_month()
    {
        $recurrence = 'monthly';
        $dateBefore = Carbon::create(2020, 1, 1);
        $dateAfter = Carbon::create(2020, 2, 1);

        // Assert date has been forwarded correctly
        $this->assertDatesEqual(
            $dateAfter,
            (new RecurrencesSupport())->forwardDateByRecurrence($dateBefore, $recurrence)
        );

        // Also assert that original date has not been altered
        $this->assertDatesEqual($dateAfter->subMonth(), $dateBefore);
    }

    /** @test */
    public function quarterly_recurrence_forwards_date_3_months()
    {
        $recurrence = 'quarterly';
        $dateBefore = Carbon::create(2020, 1, 26);
        $dateAfter = Carbon::create(2020, 4, 26);

        // Assert date has been forwarded correctly
        $this->assertDatesEqual(
            $dateAfter,
            (new RecurrencesSupport())->forwardDateByRecurrence($dateBefore, $recurrence)
        );

        // Also assert that original date has not been altered
        $this->assertDatesEqual($dateAfter->subQuarter(), $dateBefore);
    }

    /** @test */
    public function yearly_recurrence_forwards_date_on_a_year()
    {
        $recurrence = 'yearly';
        $dateBefore = Carbon::create(2020, 3, 12);
        $dateAfter = Carbon::create(2021, 3, 12);

        // Assert date has been forwarded correctly
        $this->assertDatesEqual(
            $dateAfter,
            (new RecurrencesSupport())->forwardDateByRecurrence($dateBefore, $recurrence)
        );

        // Also assert that original date has not been altered
        $this->assertDatesEqual($dateAfter->subYear(), $dateBefore);
    }

    /**
     * @param Carbon $before
     * @param Carbon $after
     */
    protected function assertDatesEqual(Carbon $before, Carbon $after)
    {
        $this->assertEquals($before, $after);
    }
}
