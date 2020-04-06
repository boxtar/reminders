<?php

namespace App\Helpers;

use App\Domain\Recurrences\RecurrencesSupport;
use DateTime;
use DatePeriod;
use DateInterval;

class Dates
{
    public static function days()
    {
        return [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
    }

    public static function months()
    {
        return [
            0 => 'January',
            1 => 'February',
            2 => 'March',
            3 => 'April',
            4 => 'May',
            5 => 'June',
            6 => 'July',
            7 => 'August',
            8 => 'September',
            9 => 'October',
            10 => 'November',
            11 => 'December',
        ];
    }

    /**
     * Returns true if provide day is valid. The valid day numbers and
     * their corresponding day are defined in the static days method.
     * 
     * @param string|int $day - The number representing the day of the week.
     */
    public static function isValidDay($day)
    {
        return isset(self::days()[(int) $day]);
    }

    /**
     * Returns true if provide month is valid. The valid month numbers and
     * their corresponding month are defined in the static months method.
     * 
     * @param string|int $month - The number representing the month.
     */
    public static function isValidMonth($month)
    {
        return isset(self::months()[(int) $month]);
    }

    public static function dates()
    {
        return array_map(function ($date) {
            return self::ordinal($date);
        }, range(1, 31));
    }

    public static function ordinal($date)
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

        if ((($date % 100) >= 11) && (($date % 100) <= 13))
            return $date . 'th';

        return $date . $ends[$date % 10];
    }

    /**
     * Bare minimum date validation. Simply validates that the provided
     * date is at least 1 or at max 31. This does not constrain the day
     * based on a given month (e.g. 30 for November)
     * 
     * @param string|int $date - The date to be checked
     */
    public static function isValidDate($date)
    {
        return $date > 0 && $date < 32;
    }

    /**
     * Returns a PHP DatePeriod which is a range from $start time to
     * $end time using the provided $interval which defaults to
     * 'PT30M' which is 30 minute intervals/steps.
     * 
     * @param string $start     - Start time of range (ex. '00:00' for the first hour of a day)
     * @param string $end       - End time of range (ex. '24:00' for the last hour of a day)
     * @param string $interval  - Interval to use for the time range (defaults to 30 minutes)
     */
    public static function timeRange($start, $end, $interval = 'PT30M')
    {
        return new DatePeriod(
            new DateTime($start),
            new DateInterval($interval),
            new DateTime($end)
        );
    }

    public static function times()
    {
        $times = [];
        foreach (self::timeRange("00:00", "24:00") as $timePeriod) {
            $times[] = date_format($timePeriod, "H:i");
        }
        return $times;
    }

    public static function isValidTime($time)
    {
        // Time will be in format "HH:MM". Split into Hour and Minute parts using colon.
        // Then map over the resulting array and cast each string part to int.
        [$hour, $minute] = array_map(function ($part) {
            return (int) $part;
        }, explode(":", $time));
        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) return false;
        return true;
    }

    public static function getAll()
    {
        return [
            'frequencies' => RecurrencesSupport::frequencies(),
            'days' => self::days(),
            'dates' => self::dates(),
            'times' => self::times()
        ];
    }
}
