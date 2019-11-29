<?php

namespace App\Services\Scheduler;

use Carbon\Carbon;
use Cron\CronExpression;

abstract class Task
{
    use Frequencies;
    
    public $expression = "* * * * *";

    abstract public function handle();

    /**
     * Checks if the instances cron expression is due to run based on
     * the provided date.
     * 
     * @param Carbon $date
     * @return bool
     */
    public function isDueToRun(Carbon $date)
    {
        return CronExpression::factory($this->expression)
            ->isDue($date);
    }
}
