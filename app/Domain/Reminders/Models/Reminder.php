<?php

namespace App\Domain\Reminders\Models;

use App\Models\User;
use App\Domain\Dates\DatesSupport;
use App\Domain\Recurrences\Exceptions\InvalidRecurrenceException;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Recurrences\RecurrencesSupport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body', 'frequency',
        'day', 'date', 'month',
        'year', 'hour', 'minute',
        'expression', 'recurrence_expression',
        'channels', 'is_recurring', 'initial_reminder_run',
        'next_run'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'initial_reminder_run' => 'boolean',
        'is_recurring' => 'boolean',
        'channels' => 'array',
    ];

    protected $dates = ['next_run'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['reminder_date'];

    /**
     * Scope a query to only include reminders that can be sent.
     * Reminders can only be sent if:
     * their next run date is in the past
     * AND
     * (initial reminder is false OR is recurring is true)
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCanBeSent($query)
    {
        return $query->where('next_run', '<', \Carbon\Carbon::now('Europe/London'))
            ->where(function ($query) {
                $query
                    ->where('initial_reminder_run', false)
                    ->orWhere('is_recurring', true);
            });
    }

    /**
     * @return User
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor for frequency attribute
     * 
     * @param string
     * @return string
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
     * @return string
     */
    public function getDayAttribute()
    {
        if (!$day = DatesSupport::makeDay($this->next_run->dayOfWeek)) {
            return "Invalid day";
        }

        return ucfirst($day->getDayName());
    }

    /**
     * Accessor for date attribute
     * 
     * @return string
     */
    public function getDateAttribute()
    {
        if (!DatesSupport::isValidDate($this->next_run->day))
            return "Invalid date";
        return DatesSupport::makeOrdinal($this->next_run->day);
    }

    /**
     * Accessor for month attribute
     * 
     * @return string
     */
    public function getMonthAttribute()
    {
        $month = DatesSupport::makeMonth($this->next_run->month - 1);
        if (!$month)
            return "Invalid month";
        return ucfirst($month->getMonthName());
    }

    /**
     * Accessor for year attribute
     * 
     * @return int
     */
    public function getYearAttribute()
    {
        return $this->next_run->year;
    }

    /**
     * Accessor for hour attribute
     * 
     * @return int
     */
    public function getHourAttribute()
    {
        return $this->next_run->hour;
    }

    /**
     * Accessor for minute attribute
     * 
     * @return int
     */
    public function getMinuteAttribute()
    {
        return $this->next_run->minute;
    }

    /**
     * Returns the reminder Date and Time in format YmdHi
     * which is useful for sorting.
     * 
     * @return string
     */
    public function getReminderDateAttribute()
    {
        return $this->next_run->format('YmdHi');
    }

    public function getIsRecurringAttribute($is_recurring)
    {
        return (bool) $is_recurring;
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

    /**
     * Proxies to getIsRecurringAttribute accessor
     * 
     * @return bool
     */
    public function isRecurring()
    {
        return $this->is_recurring;
    }

    /**
     * Set the recurrence frequency.
     * 
     * @param string $frequency
     * @return self
     * @throws InvalidRecurrenceException
     */
    public function setRecurrenceFrequency($frequency)
    {
        if (!RecurrencesSupport::isFrequencyValid($frequency))
            throw new InvalidRecurrenceException("$frequency is not a valid frequency");

        $this->is_recurring = true;
        $this->frequency = $frequency;
        $this->save();
        return $this;
    }

    /**
     * Removes recurrence
     * 
     * @return self
     */
    public function removeRecurrence()
    {
        $this->is_recurring = false;
        $this->frequency = 'none';
        $this->save();
        return $this;
    }

    /**
     * Forwards next_run date on according to frequency attribute
     * 
     * @return self
     */
    public function forwardNextRunDate()
    {
        // Forward next_run date based on frequency
        $this->next_run = (new RecurrencesSupport)
            ->forwardDateByRecurrence($this->next_run, $this->attributes['frequency']);

        // If the new next_run date is in past, then update again based on now
        $now = Carbon::parse(Carbon::now('Europe/London')->format("Y/m/d H:i"));
        if ($this->next_run < $now) {
            $this->next_run = (new RecurrencesSupport)
                ->forwardDateByRecurrence($now, $this->attributes['frequency']);
        }


        $this->save();
        return $this;
    }

    /**
     * Returns the prevailing frequency.
     * Initial Reminder Expression if initial hasn't run yet
     * and Recurrence Expression if initial has already run.
     * 
     * @return string|bool
     */
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
