<?php

namespace App\Jobs;

use App\Domain\Reminders\Models\Reminder;
use Carbon\Carbon;

class SetNextRunDate
{
    public function __construct()
    {
    }

    public function run()
    {
        Reminder::all()->each(function (Reminder $reminder) {
            if ($expression = $reminder->getCronExpression()) {
                $year = 2020;
                [$minute, $hour, $day, $month] = explode(" ", $expression);
                $date = Carbon::create($year, $month, $day, $hour, $minute, 0);
                $reminder->next_run = $date;
                $reminder->save();
            }
        });
    }
}
