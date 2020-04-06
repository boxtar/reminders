<?php

namespace App\Services\Scheduler;

// TODO:
// The fact that this package is using application specific functionality
// says that there is a bad decision made here. This class should either
// be moved to an application specific class or the Dates helper class
// should be made part of this Scheduler Service.
use App\Helpers\Dates;

class FrequencyBuilder extends Task
{

    /**
     * Handle the task
     */
    public function handle()
    {
        //
    }

    /**
     * The frequency/interval to run at.
     * @param string $frequency - 'daily', 'weekly', 'monthly', 'yearly'.
     */
    public function frequency($frequency)
    {
        if (method_exists($this, $frequency)) {
            $this->{$frequency}();
        }
        return $this;
    }

    /**
     * The day of the week to set the reminder to run on.
     * 
     * @param string $day.
     */
    public function day($day)
    {
        if ($day && Dates::isValidDay($day)) {
            $this->days($day);
        }

        return $this;
    }

    /**
     * The date of the month to set the reminder to run on.
     * 
     * @param string $date.
     */
    public function date($date)
    {
        if ($date && Dates::isValidDate($date)) {
            $this->monthlyOn($date);
        }

        return $this;
    }

    /**
     * The month on which the reminder should run.
     * 
     * @param string $date.
     */
    public function month($month)
    {
        if ($month && Dates::isValidMonth($month)) {
            $this->yearlyOn($month);
        }
        return $this;
    }

    /**
     * The time of the day to set the reminder to run
     * 
     * @param string|int $hour
     * @param string|int $minute
     */
    public function time($hour, $minute)
    {
        $this->at($hour, $minute);

        return $this;
    }

    public function getExpression()
    {
        return $this->expression;
    }
}
