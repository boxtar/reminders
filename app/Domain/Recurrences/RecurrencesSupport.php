<?php

namespace App\Domain\Recurrences;

use Carbon\Carbon;
use App\Domain\Recurrences\Exceptions\InvalidRecurrenceException;

class RecurrencesSupport
{

    /**
     * Returns an array of recognised frequencies.
     * 'none' is for indicating that no recurrence is required. I think
     * there should be a better way of doing this. Perhaps a checkbox
     * on frontend... Then controller decides whether to build the
     * recurrence frequency or not...
     */
    public static function frequencies()
    {
        return [
            'hourly' => 'Every hour',
            'daily' => 'Every day',
            'weekdays' => 'Weekdays only',
            'weekly' => 'Every week',
            'monthly' => 'Every month',
            'quarterly' => "Every 3 months",
            'yearly' => 'Every year'
        ];
    }

    /**
     * Returns true if provided frequency is a valid recurrence frequency.
     * 
     * @param string the frequency to be checked
     * @return bool
     */
    public static function isFrequencyValid($frequency)
    {
        return in_array($frequency, array_keys(self::frequencies()));
    }

    /**
     * @param Carbon $date
     * @param string $recurrence
     * @return Carbon
     */
    public function forwardDateByRecurrence(Carbon $date, string $recurrence)
    {
        if (!method_exists($this, $recurrence)) {
            throw new InvalidRecurrenceException("$recurrence is not a valid recurrence");
        }
        return $this->{$recurrence}($date);
    }

    /**
     * Forward provided datetime on by 1 hour
     * 
     * @param Carbon $date 
     * @return Carbon
     */
    protected function hourly(Carbon $date)
    {
        return $date->copy()->addHour();
    }

    /**
     * Forward provided date on by 1 day
     * 
     * @param Carbon $date 
     * @return Carbon
     */
    protected function daily(Carbon $date)
    {
        return $date->copy()->addDay();
    }

    /**
     * Forward provided date on by 1 day, but skip weekends.
     * 
     * @param Carbon $date 
     * @return Carbon
     */
    protected function weekdays(Carbon $date)
    {
        $date = $date->copy()->addDay();
        while ($date->isWeekend()) $date->addDay();
        return $date;
    }

    /**
     * Forward provided date on by 1 week
     * 
     * @param Carbon $date 
     * @return Carbon
     */
    protected function weekly(Carbon $date)
    {
        return $date->copy()->addWeek();
    }

    /**
     * Forward provided date on by 1 month
     * 
     * @param Carbon $date 
     * @return Carbon
     */
    protected function monthly(Carbon $date)
    {
        return $date->copy()->addMonth();
    }

    /**
     * Forward provided date on by 3 months
     * 
     * @param Carbon $date 
     * @return Carbon
     */
    protected function quarterly(Carbon $date)
    {
        return $date->copy()->addQuarter();
    }

    /**
     * Forward provided date on by 1 year
     * 
     * @param Carbon $date 
     * @return Carbon
     */
    protected function yearly(Carbon $date)
    {
        return $date->copy()->addYear();
    }
}
