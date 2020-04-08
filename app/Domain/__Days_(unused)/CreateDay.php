<?php

namespace App\Domain\Days;

class CreateDay
{
    /**
     * @param number - Day number of the week (0-6)
     * @throws \Exception
     */
    public static function fromDayNumber($day)
    {
        switch ($day) {
            case 0:
                return new Sunday;
                break;

            case 1:
                return new Monday;
                break;

            case 2:
                return new Tuesday;
                break;

            case 3:
                return new Wednesday;
                break;

            case 4:
                return new Thursday;
                break;

            case 5:
                return new Friday;
                break;

            case 6:
                return new Saturday;
                break;

            default:
                throw new \Exception("Invalid day number: {$day}");
                break;
        }
    }
}
