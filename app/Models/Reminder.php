<?php

namespace App\Models;

use App\Domain\Recurrences\RecurrencesSupport;
use App\Helpers\Dates;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use SoftDeletes;
    /**
     * Blacklist (better to use $fillable whitelist, but this will do for the course)
     */
    protected $fillable = [
        'body', 'frequency',
        'day', 'date', 'month',
        'year', 'hour', 'minute',
        'expression', 'recurrence_expression',
        'channels', 'is_recurring',
    ];

    protected $casts = [
        'initial_reminder_run' => 'boolean',
        'is_recurring' => 'boolean',
        'channels' => 'array'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor for frequency attribute
     * 
     * @param string $frequency
     */
    public function getFrequencyAttribute($frequency)
    {
        if ($this->is_recurring) {
            return RecurrencesSupport::frequencies()[$frequency];
        }
        return $frequency;
    }

    /**
     * Accessor for day attribute
     * 
     * @param string $frequency
     */
    public function getDayAttribute($day)
    {
        if (!$day)
            return false;

        return Dates::days()[$day];
    }

    /**
     * Accessor for date attribute
     * 
     * @param string $frequency
     */
    public function getDateAttribute($date)
    {
        if (!$date)
            return false;

        return Dates::ordinal($date);
    }

    /**
     * Accessor for month attribute
     * 
     * @param string $frequency
     */
    public function getMonthAttribute($month)
    {
        if (!$month)
            return false;

        return Dates::months()[$month];
    }

    /**
     * Returns the Carbon instance of the Reminder date.
     * TODO: Look into using different timezones
     * 
     * @return Carbon
     */
    // public function getCarbonDate()
    // {
    //     return Carbon::create($this->year, $this->month + 1, $this->date, $this->hour, $this->minute, 'Europe/London');
    // }

    /**
     * Uses Carbon api: https://carbon.nesbot.com/docs/#api-getters
     * 0 (Sun) to 6 (Sat)
     */
    // public function getDayOfWeek()
    // {
    //     return ($this->getCarbonDate())->dayOfWeek;
    // }

    public function hasInitialReminderRun()
    {
        return $this->initial_reminder_run;
    }

    public function markInitialReminderComplete()
    {
        $this->initial_reminder_run = true;
        $this->save();
    }

    public function isRecurring()
    {
        return $this->is_recurring;
    }

    public function getCronExpression()
    {
        if (!$this->hasInitialReminderRun()) {
            return $this->expression;
        } else if ($this->isRecurring()) {
            return $this->recurrence_expression;
        } else {
            return false;
        }
    }
}
