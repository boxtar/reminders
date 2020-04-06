<?php

namespace App\Domain\Dates;

class DatesSupport
{
    /**
     * Takes a time in a string format and returns an array with 
     * the hour and minute.
     * 
     * @param String $time - e.g. "09:30"
     * @return Array - e.g. [9, 30]
     */
    public static function extractTimeValues($time)
    {
        [$hour, $minute] = explode(":", $time);
        $hour = (int) $hour;
        $minute = (int) $minute;
        return [$hour, $minute];
    }

    /**
     * @param number $date - date of the month (1st - 31st)
     * @param number $month - Jan to Dec (1 - 12)
     * @return bool
     */
    public static function isDateValidForMonth($date, $month)
    {
        if ($month == 2) {
            // February
            if ($date > 28) return false;
        } else if (in_array($month, [4, 6, 9, 11])) {
            // Other months with 30 days
            if ($date > 30) return false;
        }
        return true;
    }

    public static function getLastDateForMonth($month)
    {
        if ($month == 2) {
            // February
            return 28;
        }
        else if (in_array($month, [4, 6, 9, 11])) {
            // Other months with 30 days
            return 30;
        }
        return 31;
    }
}
