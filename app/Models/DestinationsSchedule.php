<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationsSchedule extends Model
{
    protected $table = 'destinations_schedules';
    protected $appends = ['name'];
    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * Get the name of the day
     * 
     * Usage: $schedule->name
     * Thanks to $appends property in model we can access this attribute as a property
     * This is better for translating, because it does not interfere
     * with updating this model in database
     * 
     */
    public function getNameAttribute(): string
    {
        return __("day." . $this->day_of_week);
    }

    /**
     * Function for HTM?L purposes to check checkbox if destination is closed
     */
    public function checked(): string
    {
        if($this->is_closed) {
            return 'checked';
        } else {
            return '';
        }
    }
}
