<?php

namespace App\Helpers;

use DateTime;
use DatePeriod;
use DateInterval;

class Dates
{
    public static function frequencies()
    {
        return [
            'daily' => 'Every Day',
            'weekly' => 'Every Week',
            'monthly' => 'Every Month'
        ];
    }

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

    /**
     * Returns true if provide day is valid. The valid day numbers and
     * their corresponding day are defined in the static days method.
     * 
     * @param string|int $day - The number represeting the day of the week.
     */
    public static function isValidDay($day)
    {
        return isset(self::days()[(int) $day]);
    }

    public static function dates() {
        return array_map(function($date) {
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
    public static function isValidDate($date) {
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

    public static function times() {
        $times = [];
        foreach (self::timeRange("00:00", "24:00") as $timePeriod) {
            $times[] = date_format($timePeriod, "H:i");
        }
        return $times;
    }

    public static function getAll() {
        return [
            'frequencies' => self::frequencies(),
            'days' => self::days(),
            'dates' => self::dates(),
            'times' => self::times()
        ];
    }
}
