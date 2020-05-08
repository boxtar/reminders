<?php

namespace Tests\Domain\Dates;

use Tests\TestCase;
use App\Domain\Dates\DatesSupport;
use App\Domain\Dates\Entities\Days\Friday;
use App\Domain\Dates\Entities\Days\Monday;
use App\Domain\Dates\Entities\Days\Saturday;
use App\Domain\Dates\Entities\Days\Sunday;
use App\Domain\Dates\Entities\Days\Thursday;
use App\Domain\Dates\Entities\Days\Tuesday;
use App\Domain\Dates\Entities\Days\Wednesday;
use App\Domain\Dates\Entities\Months\April;
use App\Domain\Dates\Entities\Months\August;
use App\Domain\Dates\Entities\Months\December;
use App\Domain\Dates\Entities\Months\February;
use App\Domain\Dates\Entities\Months\January;
use App\Domain\Dates\Entities\Months\July;
use App\Domain\Dates\Entities\Months\June;
use App\Domain\Dates\Entities\Months\March;
use App\Domain\Dates\Entities\Months\May;
use App\Domain\Dates\Entities\Months\November;
use App\Domain\Dates\Entities\Months\October;
use App\Domain\Dates\Entities\Months\September;
use App\Domain\Reminders\ReminderData;

class DatesSupportTest extends TestCase
{
    /** @test */
    public function test_is_valid_day()
    {
        // false and null should not be interpreted as Monday
        $this->assertFalse(DatesSupport::isValidDay(false));
        $this->assertFalse(DatesSupport::isValidDay(null));

        // Monday through Sunday
        foreach (range(0, 6) as $day) {
            $this->assertTrue(DatesSupport::isValidDay($day));
        }

        // out of bounds testing
        $this->assertFalse(DatesSupport::isValidDay(-1));
        $this->assertFalse(DatesSupport::isValidDay(7));
    }

    /** @test */
    public function test_make_day()
    {
        // Invalid days
        $this->assertFalse(DatesSupport::makeDay(-1));
        $this->assertFalse(DatesSupport::makeDay(8));

        // Valid days
        $this->assertInstanceOf(Sunday::class, DatesSupport::makeDay(0));
        $this->assertInstanceOf(Monday::class, DatesSupport::makeDay(1));
        $this->assertInstanceOf(Tuesday::class, DatesSupport::makeDay(2));
        $this->assertInstanceOf(Wednesday::class, DatesSupport::makeDay(3));
        $this->assertInstanceOf(Thursday::class, DatesSupport::makeDay(4));
        $this->assertInstanceOf(Friday::class, DatesSupport::makeDay(5));
        $this->assertInstanceOf(Saturday::class, DatesSupport::makeDay(6));
    }

    /** @test */
    public function test_is_valid_month()
    {
        // false and null should not be interpreted as Monday
        $this->assertFalse(DatesSupport::isValidMonth(false));
        $this->assertFalse(DatesSupport::isValidMonth(null));

        // January through December
        foreach (range(0, 11) as $month) {
            $this->assertTrue(DatesSupport::isValidMonth($month));
        }

        // out of bounds testing
        $this->assertFalse(DatesSupport::isValidMonth(-1));
        $this->assertFalse(DatesSupport::isValidMonth(12));
    }

    /** @test */
    public function test_make_month()
    {
        // Invalid months
        $this->assertFalse(DatesSupport::makeMonth(-1));
        $this->assertFalse(DatesSupport::makeMonth(12));

        // Valid months
        $this->assertInstanceOf(January::class, DatesSupport::makeMonth(0));
        $this->assertInstanceOf(February::class, DatesSupport::makeMonth(1));
        $this->assertInstanceOf(March::class, DatesSupport::makeMonth(2));
        $this->assertInstanceOf(April::class, DatesSupport::makeMonth(3));
        $this->assertInstanceOf(May::class, DatesSupport::makeMonth(4));
        $this->assertInstanceOf(June::class, DatesSupport::makeMonth(5));
        $this->assertInstanceOf(July::class, DatesSupport::makeMonth(6));
        $this->assertInstanceOf(August::class, DatesSupport::makeMonth(7));
        $this->assertInstanceOf(September::class, DatesSupport::makeMonth(8));
        $this->assertInstanceOf(October::class, DatesSupport::makeMonth(9));
        $this->assertInstanceOf(November::class, DatesSupport::makeMonth(10));
        $this->assertInstanceOf(December::class, DatesSupport::makeMonth(11));
    }

    /** @test */
    public function test_is_valid_date()
    {
        // Beyond bounds
        $this->assertFalse(DatesSupport::isValidDate(0));
        $this->assertFalse(DatesSupport::isValidDate(32));

        // Valid dates test
        foreach (range(1, 31) as $date) {
            $this->assertTrue(DatesSupport::isValidDate($date));
        }
    }

    /** @test */
    public function test_is_date_valid_for_month()
    {
        // Invalid date
        $this->assertFalse(DatesSupport::isDateValidForMonth(33, 1));
        // Invalid month
        $this->assertFalse(DatesSupport::isDateValidForMonth(1, 20));

        // January
        $this->assertTrue(DatesSupport::isDateValidForMonth(1, 0));
        $this->assertTrue(DatesSupport::isDateValidForMonth(31, 0));
        $this->assertFalse(DatesSupport::isDateValidForMonth(32, 0));

        // February
        $this->assertTrue(DatesSupport::isDateValidForMonth(28, 1));
        $this->assertFalse(DatesSupport::isDateValidForMonth(31, 1));

        // April
        $this->assertTrue(DatesSupport::isDateValidForMonth(1, 3));
        $this->assertTrue(DatesSupport::isDateValidForMonth(30, 3));
        $this->assertFalse(DatesSupport::isDateValidForMonth(31, 3));
    }

    /** @test */
    public function test_is_valid_time()
    {
        $time = "00:00";
        $this->assertTrue(DatesSupport::isValidTime($time));

        // Invalid time
        $time = "29:67";
        $this->assertFalse(DatesSupport::isValidTime($time));

        $time = "not a time";
        $this->assertFalse(DatesSupport::isValidTime($time));
    }

    /** @test */
    public function test_ordinal_dates()
    {
        // ["1st", "2nd", ..., "31st"]
        $ordinalDates = DatesSupport::ordinalDates();
        $this->assertCount(31, $ordinalDates);
        $this->assertEquals("1st", $ordinalDates[0]);
        $this->assertEquals("31st", $ordinalDates[count($ordinalDates) - 1]);
    }

    /** @test */
    public function test_make_ordinal()
    {
        $date = 1;
        $this->assertEquals("1st", DatesSupport::makeOrdinal($date));

        $date = 2;
        $this->assertEquals("2nd", DatesSupport::makeOrdinal($date));

        $date = 3;
        $this->assertEquals("3rd", DatesSupport::makeOrdinal($date));

        $date = 4;
        $this->assertEquals("4th", DatesSupport::makeOrdinal($date));

        // Yes, this will work as the function makes any number ordinal.
        $date = 32;
        $this->assertEquals("32nd", DatesSupport::makeOrdinal($date));
    }

    /** @test */
    public function test_get_last_date_for_month()
    {
        // January
        $this->assertEquals(31, DatesSupport::getLastDateForMonth(0));
        $this->assertEquals(31, DatesSupport::getLastDateForMonth(new January));

        // February
        $this->assertEquals(28, DatesSupport::getLastDateForMonth(1));
        $this->assertEquals(28, DatesSupport::getLastDateForMonth(new February));

        // April
        $this->assertEquals(30, DatesSupport::getLastDateForMonth(3));
        $this->assertEquals(30, DatesSupport::getLastDateForMonth(new April));
    }

    /** @test */
    public function test_extract_time_values()
    {
        $time = "5:55";
        $timeparts = DatesSupport::extractTimeValues($time);
        $this->assertIsArray($timeparts);
        $this->assertEquals(5, $timeparts[0]);
        $this->assertEquals(55, $timeparts[1]);

        // interpreted as "0:55"
        $time = ":55";
        $timeparts = DatesSupport::extractTimeValues($time);
        $this->assertIsArray($timeparts);
        $this->assertEquals(0, $timeparts[0]);
        $this->assertEquals(55, $timeparts[1]);

        // interpreted as "3:0"
        $time = "3:";
        $timeparts = DatesSupport::extractTimeValues($time);
        $this->assertIsArray($timeparts);
        $this->assertEquals(3, $timeparts[0]);
        $this->assertEquals(0, $timeparts[1]);

        $time = false;
        $this->expectException(\Exception::class);
        DatesSupport::extractTimeValues($time);
    }

    /** @test */
    public function test_is_date_time_in_past()
    {
        $carbonDate = \Carbon\Carbon::now(new \DateTimeZone('Europe/London'));

        // Assert a date way in the past is definitely in the past.
        $this->assertTrue(DatesSupport::isDateAndTimeInThePast(2020, 01, 01));

        // Subtract a minute from now
        $carbonDate->subMinute();
        $data = $this->makeReminderData($carbonDate);
        $this->assertTrue(DatesSupport::isDateAndTimeInThePast($data->year, $data->month, $data->date, $data->time));
        $carbonDate->addMinute(); // Bring back

        // Add a day - should NOT be in the past
        $carbonDate->addDay();
        $data = $this->makeReminderData($carbonDate);
        $this->assertFalse(DatesSupport::isDateAndTimeInThePast($data->year, $data->month, $data->date, $data->time));
        $carbonDate->subDay(); // Bring back to today

        // Add a month - should NOT be in the past
        $carbonDate->addMonth();
        $data = $this->makeReminderData($carbonDate);
        $this->assertFalse(DatesSupport::isDateAndTimeInThePast($data->year, $data->month, $data->date, $data->time));
        $carbonDate->subMonth(); // Bring back to today

        // Add a year - should NOT be in the past
        $carbonDate->addYear();
        $data = $this->makeReminderData($carbonDate);
        $this->assertFalse(DatesSupport::isDateAndTimeInThePast($data->year, $data->month, $data->date, $data->time));
        $carbonDate->subYear(); // Bring back to today

        // Add an hour - should NOT be in the past
        $carbonDate->addHour();
        $data = $this->makeReminderData($carbonDate);
        $this->assertFalse(DatesSupport::isDateAndTimeInThePast($data->year, $data->month, $data->date, $data->time));
        $carbonDate->subHour(); // Bring back to this time

        // Add a minute - should NOT be in the past
        $carbonDate->addMinute();
        $data = $this->makeReminderData($carbonDate);
        $this->assertFalse(DatesSupport::isDateAndTimeInThePast($data->year, $data->month, $data->date, $data->time));
        $carbonDate->subMinute(); // Bring back to this time
    }
}
