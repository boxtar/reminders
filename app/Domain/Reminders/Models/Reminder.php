<?php

namespace App\Domain\Reminders\Models;

use App\Models\User;
use App\Domain\Dates\DatesSupport;
use App\Domain\Recurrences\Exceptions\InvalidRecurrenceException;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Recurrences\RecurrencesSupport;
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
     * @param int
     * @return string
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
     * @param int
     * @return string
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
     * @param int
     * @return string
     */
    public function getMonthAttribute($month)
    {
        $month = DatesSupport::makeMonth($month);
        if (!$month)
            return "Invalid month";
        return ucfirst($month->getMonthName());
    }

    /**
     * Returns the reminder Date and Time in format YYYYMMDDHHMM
     * which is useful for sorting.
     * 
     * @return string
     */
    public function getReminderDateAttribute()
    {
        $attributes = (object) $this->getAttributes();

        // Keys required from the Reminder instance
        $keys = ['year', 'month', 'date', 'hour', 'minute'];

        // This will be the return value. It is built below.
        $dateTimeString = "";

        // Pad the above attributes to 2 digits, if required.
        // Build up the dateTimeString.
        foreach ($keys as $key) {
            $attributes->{$key} = $attributes->{$key} < 10 ? "0" . $attributes->{$key} : $attributes->{$key};
            $dateTimeString .= (string) $attributes->{$key};
        }

        return $dateTimeString;
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
     * @return self
     */
    public function forwardNextRunDate()
    {
        $this->next_run = (new RecurrencesSupport)->forwardDateByRecurrence($this->next_run, $this->attributes['frequency']);
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
