<?php

namespace App\Scheduler;

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
     * Defaults to midnight (00:00)
     * 
     * @param integer $hour
     * @param integer $minute
     */
    public function dailyAt($hour = 0, $minute = 0)
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
     * Defaults to midnight(00:00) and noon (12:00).
     * 
     * @param integer $firstHour
     * @param integer $lastHour
     */
    public function twiceDaily($firstHour = 0, $lastHour = 12)
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
     * 
     */
    public function weekends()
    {
        return $this->days(6, 7);
    }

    public function monthly()
    {
        return $this->monthlyOn();
    }

    public function monthlyOn($day = 1)
    {
        return $this->setIntoExpression(1, [0, 0, $day]);
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
