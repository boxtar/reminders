<?php

namespace App\Domain\Reminders\Models;

use App\Domain\Dates\DatesSupport;
use App\Domain\Recurrences\RecurrencesSupport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use SoftDeletes;

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
        return "No recurrence";
    }

    /**
     * Accessor for day attribute
     * 
     * @param string $frequency
     */
    public function getDayAttribute($day)
    {
        $day = DatesSupport::makeDay($day);
        if (!$day)
            return "Invalid day";
        return ucfirst($day->getDayName());
    }

    /**
     * Accessor for date attribute
     * 
     * @param string $frequency
     */
    public function getDateAttribute($date)
    {
        if (!DatesSupport::isValidDate($date))
            return "Invalid date";
        return DatesSupport::makeOrdinal($date);
    }

    /**
     * Accessor for month attribute
     * 
     * @param string $frequency
     */
    public function getMonthAttribute($month)
    {
        $month = DatesSupport::makeMonth($month);
        if(!$month)
            return "Invalid month";
        return ucfirst($month->getMonthName());
    }

    public function hasInitialReminderRun()
    {
        return (bool) $this->initial_reminder_run;
    }

    public function markInitialReminderComplete()
    {
        $this->initial_reminder_run = true;
        $this->save();
    }

    public function isRecurring()
    {
        return (bool) $this->is_recurring;
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
