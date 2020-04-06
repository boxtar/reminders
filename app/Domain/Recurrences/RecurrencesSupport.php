<?php

namespace App\Domain\Recurrences;

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
            'daily' => 'Every day',
            'weekly' => 'Every week',
            'monthly' => 'Every month',
            'quarterly' => "Every 3 months",
            'yearly' => "Every year"
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
}
