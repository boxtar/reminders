<?php

namespace App\Services\Scheduler;

use App\Helpers\Utils;

/**
 * This will hold all the cron frequency logic
 */
trait Frequencies
{
    /**
     * Sets the expression so that it runs every minute
     */
    public function everyMinute()
    {
        return $this->expression = "* * * * *";
    }

    /**
     * Sets the expression so that it runs every 10 minutes
     */
    public function everyTenMinutes()
    {
        return $this->expression = "*/10 * * * *";
    }

    /**
     * Sets the expression so that it runs every 30 minutes
     */
    public function everyThirtyMinutes()
    {
        return $this->expression = "*/30 * * * *";
    }

    /**
     * Sets the instance to run every hour. This defaults to
     * the 1st minute of every hour.
     */
    public function hourly()
    {
        return $this->hourlyAt();
    }

    /**
     * Sets instance to run every hour on the given minute
     * 
     * @param integer $minute
     */
    public function hourlyAt($minute = 1)
    {
        return $this->setIntoExpression(1, (string) $minute);
    }

    /**
     * Sets instance to run every day at midnight
     * 
     */
    public function daily()
    {
        return $this->dailyAt();
    }

    /**
     * Sets instance to run every day at given time.
     * Defaults to 8am (08:00)
     * 
     * @param integer $hour
     * @param integer $minute
     */
    public function dailyAt($hour = 8, $minute = 0)
    {
        return $this->setIntoExpression(1, [$minute, $hour]);
    }

    /**
     * Sets the time to run the instance at. This can be
     * used to chain onto day modifiers to modify time.
     * 
     * @param integer $hour
     * @param integer $minute
     */
    public function at($hour = 0, $minute = 0)
    {
        return $this->dailyAt($hour, $minute);
    }

    /**
     * Sets instance to run twice every day at the 0th minute of the given hours.
     * Defaults to 8am and 2pm, because these seem like sensible defaults.
     * 
     * @param integer $firstHour
     * @param integer $lastHour
     */
    public function twiceDaily($firstHour = 8, $lastHour = 14)
    {
        return $this->setIntoExpression(1, [0, "$firstHour,$lastHour"]);
    }

    /**
     * Sets instance to run on the given days
     * 
     * @param integer|array - This is retrieved using func_get_args
     */
    public function days()
    {
        return $this
            ->daily()
            ->setIntoExpression(5, implode(",", func_get_args() ?: ["*"]));
    }

    /**
     * Sets instance to run on Mondays
     * 
     */
    public function mondays()
    {
        return $this->days(1);
    }

    /**
     * Sets instance to run on Tuesdays
     * 
     */
    public function tuesdays()
    {
        return $this->days(2);
    }

    /**
     * Sets instance to run on Wednesdays
     * 
     */
    public function wednesdays()
    {
        return $this->days(3);
    }

    /**
     * Sets instance to run on Thursdays
     * 
     */
    public function thursdays()
    {
        return $this->days(4);
    }

    /**
     * Sets instance to run on Fridays
     * 
     */
    public function fridays()
    {
        return $this->days(5);
    }

    /**
     * Sets instance to run on Saturdays
     * 
     */
    public function saturdays()
    {
        return $this->days(6);
    }

    /**
     * Sets instance to run on Sundays
     * 
     */
    public function sundays()
    {
        return $this->days(7);
    }

    /**
     * Sets instance to run on Weekdays only
     * 
     */
    public function weekdays()
    {
        return $this->days(1, 2, 3, 4, 5);
    }

    /**
     * Sets instance to run on Weekdays only
     */
    public function weekends()
    {
        return $this->days(6, 7);
    }

    /**
     * Sets instance to run every 3 days starting from the
     * given start day
     */
    public function halfWeekly($starts = 1)
    {
        $next = $starts + 3;
        return $this->days($starts, $next == 7 ? $next : $next % 7);
    }

    /**
     * Sets instance to run every week on the given day
     */
    public function weekly($day = 1)
    {
        return $this->days($day);
    }

    /**
     * This is a difficult one. Went for a hardcoded schedule
     * for simplicity:
     * https://stackoverflow.com/questions/46109358/how-to-create-a-cron-expression-for-every-2-weeks
     */
    public function halfMonthly()
    {
        $this->dailyAt(9);
        return $this->setIntoExpression(3, '1,15');
    }

    public function monthly()
    {
        return $this->monthlyOn();
    }

    /**
     * Sets expression to run once per month on the given day of the month.
     * Defaults to the 1st.
     */
    public function monthlyOn($day = 1, $minute = 1, $hour = 8)
    {
        return $this->setIntoExpression(1, [$minute, $hour, $day]);
    }

    public function quarterly($starts = 1, $minute = 1, $hour = 8, $day = 1)
    {
        $next = $starts + 3;
        $next = $next == 12 ? $next : $next % 12;
        return $this->setIntoExpression(1, [$minute, $hour, $day, "$starts,$next"]);
    }

    public function yearlyOn($month = 1)
    {
        return $this->setIntoExpression(1, [0, 0, 1, $month]);
    }

    public function yearly()
    {
        $this->yearlyOn();
    }

    /**
     * Sets a value into the instance's cron expression starting
     * from the provided position
     * 
     * @param integer $position
     * @param string|array $value
     */
    public function setIntoExpression($position, $value)
    {
        // Cast to array
        $value = (array) $value;
        // Explode instance's expression into bits
        $expressionBits = explode(' ', $this->expression);
        // Splice in the provided value
        array_splice(
            $expressionBits,
            $position - 1,
            count($value),
            $value
        );
        // Fix size of new array expression to 5, implode it into a string
        // expression and pass it to cron for setting.
        return $this->cron(implode(" ", array_slice($expressionBits, 0, 5)));
    }

    /**
     * Sets a plain cron expression on the instance
     * 
     * @param string $expression
     */
    public function cron($expression)
    {
        $this->expression = $expression;
        return $this;
    }
}
