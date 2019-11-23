<?php

namespace App\Models;

use App\Helpers\Dates;
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
        'day', 'date', 'time',
        'expression', 'run_once'
    ];

    protected $casts = [
        'run_once' => 'boolean'
    ];

    /**
     * Accessor for frequency attribute
     * 
     * @param string $frequency
     */
    public function getFrequencyAttribute($frequency)
    {
        return Dates::frequencies()[$frequency];
    }

    /**
     * Accessor for day attribute
     * 
     * @param string $frequency
     */
    public function getDayAttribute($day)
    {
        if (!$day)
            return;

        return Dates::days()[$day];
    }

    /**
     * Accessor for day attribute
     * 
     * @param string $frequency
     */
    public function getDateAttribute($date)
    {
        if (!$date)
            return;

        return Dates::ordinal($date);
    }

    public function shouldRunOnce()
    {
        return $this->run_once;
    }
}
