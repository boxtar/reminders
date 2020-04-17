<?php

namespace App\Domain\Dates;

use App\Domain\Dates\Entities\Days\Friday;
use App\Domain\Dates\Entities\Days\Monday;
use App\Domain\Dates\Entities\Days\Saturday;
use App\Domain\Dates\Entities\Days\Sunday;
use App\Domain\Dates\Entities\Days\Thursday;
use App\Domain\Dates\Entities\Days\Tuesday;
use App\Domain\Dates\Entities\Days\Wednesday;
use App\Domain\Dates\Entities\Months\April;
use App\Domain\Dates\Entities\Months\August;
use App\Domain\Dates\Entities\Months\BaseMonth;
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
use DateTimeZone;

class DatesSupport
{
    /**
     * Returns true if provide day is valid. The valid day numbers and
     * their corresponding day are defined in the static days method.
     * 
     * @param string|int $day - The number representing the day of the week.
     */
    public static function isValidDay($day)
    {
        // Need to make sure false and null are not treated as 0:
        if ($day === false || $day === null) return false;
        $sunday = new Sunday;
        $saturday = new Saturday;
        return $day >= $sunday->getDayNumber() && $day <= $saturday->getDayNumber();
    }

    /**
     * @param number - Day number of the week (0-6)
     * @throws \Exception
     */
    public static function makeDay($day)
    {
        if (!self::isValidDay($day)) return false;
        switch ($day) {
            case 0:
                return new Sunday;
            case 1:
                return new Monday;
            case 2:
                return new Tuesday;
            case 3:
                return new Wednesday;
            case 4:
                return new Thursday;
            case 5:
                return new Friday;
            case 6:
                return new Saturday;
            default:
                return false;
        }
    }

    /**
     * Returns true if provide month is valid. The valid month numbers and
     * their corresponding month are defined in the static months method.
     * 
     * @param string|int $month - The number representing the month.
     */
    public static function isValidMonth($month)
    {
        // Need to make sure false and null are not treated as 0:
        if ($month === false || $month === null) return false;
        $january = new January;
        $december = new December;
        return $month >= $january() && $month <= $december();
    }

    /**
     * @param number - Month number (0-11)
     */
    public static function makeMonth($month)
    {
        if (!self::isValidMonth($month)) return false;
        switch ($month) {
            case 0:
                return new January;
            case 1:
                return new February;
            case 2:
                return new March;
            case 3:
                return new April;
            case 4:
                return new May;
            case 5:
                return new June;
            case 6:
                return new July;
            case 7:
                return new August;
            case 8:
                return new September;
            case 9:
                return new October;
            case 10:
                return new November;
            case 11:
                return new December;
            default:
                return false;
        }
    }

    /**
     * @param number $date - date of the month (1st - 31st)
     * @return bool
     */
    public static function isValidDate($date)
    {
        if ($date >= 1 && $date <= 31) return true;
        else return false;
    }

    /**
     * @param number $date - date of the month (1st - 31st)
     * @param number $month - Jan to Dec (0 - 11)
     * @return bool
     */
    public static function isDateValidForMonth($date, $month)
    {
        $month = self::makeMonth($month);
        if (!self::isValidDate($date) || !$month) {
            return false;
        } else if ($month() == 1) {
            // February
            if ($date > 28) return false;
        } else if (in_array($month(), [3, 5, 8, 10])) {
            // Other months with 30 days
            if ($date > 30) return false;
        }
        return true;
    }

    /**
     * @param string $time - in format "HH:MM"
     */
    public static function isValidTime($time)
    {
        // Time will be in format "HH:MM". Split into Hour and Minute parts using colon.
        // Then map over the resulting array and cast each string part to int.
        try {
            [$hour, $minute] = self::extractTimeValues($time);
            if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) return false;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Returns an array of dates [1st, 2nd, ..., 31st]
     * 
     * @return array
     */
    public static function ordinalDates()
    {
        return array_map(function ($date) {
            return self::makeOrdinal($date);
        }, range(1, 31));
    }

    /**
     * Returns the provided date number with the correct suffix
     * 
     * @param integer
     * @return string
     */
    public static function makeOrdinal($date)
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

        if ((($date % 100) >= 11) && (($date % 100) <= 13))
            return $date . 'th';

        return $date . $ends[$date % 10];
    }

    /**
     * Returns last day in the given month (does not include leap years)
     * @param int|BaseMonth 
     * @return int
     * @throws \Exception
     */
    public static function getLastDateForMonth($month)
    {
        // Convert to a month instance, if required.
        if (is_int($month)) {
            $month = self::makeMonth($month);
        }

        // If month is now a month instance, return the correct last day.
        if ($month instanceof BaseMonth) {
            if ($month instanceof February) return 28;
            else if (in_array($month->getMonthName(), ["april", "june", "september", "november"])) return 30;
            else return 31;
        }

        // If we got here, then the param was not an int or a month instance so throw exception.
        throw new \Exception("Invalid month value: {$month}");
    }

    /**
     * Takes a time in a string format and returns an array with 
     * the hour and minute.
     * 
     * @param String $time - e.g. "09:30"
     * @return Array - e.g. [9, 30]
     */
    public static function extractTimeValues($time)
    {
        $parts = explode(":", $time);
        if (count($parts) < 2) throw new \Exception("Invalid time {$time}");
        [$hour, $minute] = $parts;
        $hour = (int) $hour;
        $minute = (int) $minute;
        return [$hour, $minute];
    }

    public static function isDateAndTimeInThePast($year, $month, $date, $time = "0:0")
    {
        [$hour, $minute] = self::extractTimeValues($time);
        $month += 1; // Change from 0-based to 1-based months
        // Create DateTime that is being checked for being in the past.
        $date = date_create("{$year}-{$month}-{$date} {$hour}:{$minute}", new DateTimeZone('Europe/London'));
        return $date < date_create();
    }
}
