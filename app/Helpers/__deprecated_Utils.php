<?php

namespace App\Helpers;

class Utils
{
    // Not required now that I've upgraded to version 4 of symfony/var-dumper
    // Just proxy to that instead...
    public static function dd($data)
    {
        dd($data);
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
        [$hour, $minute] = explode(":", $time);
        $hour = (int) $hour;
        $minute = (int) $minute;
        return [$hour, $minute];
    }

    public static function getCarbonDate($dateData)
    {
        // Get time out of input.
        [$hour, $minute] = self::extractTimeValues($dateData->time);

        // Note: Carbon months do not start at 0.
        // Note: Date is the day of the month (1st to 28th, 29th, 30th or 31st).
        return \Carbon\Carbon::create($dateData->year, $dateData->month + 1, $dateData->date, $hour, $minute, 'Europe/London');
    }
}
