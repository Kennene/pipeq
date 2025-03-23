<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Collection;

class Destination extends Model
{
    use HasFactory;

    protected $table = 'destinations';

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function workstations()
    {
        return $this->hasMany(Workstation::class);
    }

    public function schedules()
    {
        return $this->hasMany(DestinationsSchedule::class);
    }

    public function reasons()
    {
        return $this->hasMany(Reason::class);
    }

    /**
     * Automatically translate name attribute when retrieving like $object->name
     */
    public function getNameAttribute(): string
    {
        return __($this->getRawOriginal('name'));
    }

    /**
     * Automatically translate description attribute when retrieving like $object->description
     */
    public function getDescriptionAttribute(): string
    {
        return __($this->getRawOriginal('description'));
    }

    public static function openedNow(): Collection
    {
        return Destination::all()->filter(function ($destination) {
            return $destination->isOpenNow();
        });
    }

    /**
     * Check if destination is open now
     * 
     * @return bool
     */
    public function isOpenNow(): bool
    {
        $now = now();
        $dayOfWeek = $now->dayOfWeek; // 0 = niedziela, 6 = sobota
        $currentTime = $now->format('H:i:s');

        $schedule = $this->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule || $schedule->is_closed) {
            return false;
        }

        return $schedule->open_time <= $currentTime && $schedule->close_time >= $currentTime;
    }

    /**
     * Get next opening info
     * 
     * @return string
     */
    public function getNextOpeningInfo(): string
    {
        // current day of week
        $day = now()->dayOfWeek;
        $current_day_schedule = $this->schedules()->where('day_of_week', $day)->first();

        // todo: do it better. There might be no record of current day in database.
        if($current_day_schedule == null) {
            return "unknown";
        }

        // if it's closed today or no record of the day
        if (!$current_day_schedule->is_closed) {
            // but may be will be open later?
            if(now()->format('H:i:s') < $current_day_schedule->open_time) {
                return __('time-restricted.opens_today', ['time' => date('H:i', strtotime($current_day_schedule->open_time))]);
            }
        }

        for ($i = 0; $i < 7; $i++) {
            // days are numbered from 0 to 6 where 0 is Sunday and 6 is Saturday
            if ($day == 6) {
                $day = 0;
            } else {
                $day++;
            }

            // get schedule for this day
            $current_day_schedule = $this->schedules()->where('day_of_week', $day)->first();

            // if this day is not closed return it's open_time
            if (!$current_day_schedule->is_closed) {
                $opens = $current_day_schedule->open_time;

                return __('time-restricted.opens_on', [
                    'day' => __("day.{$day}"),
                    'time' => date('H:i', strtotime($opens))
                ]);
            }
        }

        // edge case if whole week is_closed
        return __("time-restricted.closed-indefinitely");
    }
}
