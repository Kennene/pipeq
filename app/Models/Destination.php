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

    protected static function booted()
    {
        // Make sure each destination has 7 days schedule, one for every day of the week
        static::retrieved(function (Destination $destination) {
            if ($destination->schedules->count() < 7) {
                $destination->repairSchedule();
            }
        });
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

    public static function withOpeningHours(): Collection
    {
        $destinations = Destination::with('schedules')->get()->each(function ($destination) {
            $destination->opens = $destination->opens();
        });

        return $destinations;
    }

    public static function openedNow(): Collection
    {
        return Destination::with('schedules')->get()->filter(function ($destination) {
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

        $schedule = $this->schedules->firstWhere('day_of_week', $dayOfWeek);

        if (!$schedule || $schedule->is_closed) {
            return false;
        }

        return $schedule->open_time <= $currentTime && $schedule->close_time >= $currentTime;
    }

    /**
     * Function returns timestamp (int) when destination will be open
     *
     * It will return now()->timestamp if destination is open now
     *
     * @return int|null
     */
    public function opens(): ?int
    {
        // if destination is open now return now timestamp
        if ($this->isOpenNow()) {
            return now()->timestamp;
        }

        $now = now();
        $current_time = $now->format('H:i:s');
        $current_day = $now->dayOfWeek;

        $schedules = $this->schedules->keyBy('day_of_week');

        $today_schedule = $schedules->get($current_day);

        if (
            $today_schedule &&
            !$today_schedule->is_closed &&
            $today_schedule->open_time > $current_time
        ) {
            $openTime = $now->copy()->setTimeFromTimeString($today_schedule->open_time);
            return $openTime->timestamp;
        }

        for ($i = 1; $i <= 7; $i++) {
            $next_day = ($current_day + $i) % 7;
            $schedule = $schedules->get($next_day);

            if ($schedule && !$schedule->is_closed && $schedule->open_time !== null) {
                $openTime = $now->copy()
                    ->addDays($i)
                    ->setTimeFromTimeString($schedule->open_time);
                return $openTime->timestamp;
            }
        }

        return null;
    }


    /**
     * Attempt to repair schedule for destination
     * 
     * It will add missing days to destinations schedule
     * 
     * @return void
     */
    public function repairSchedule(): void
    {
        $existing_days = $this->schedules()->pluck('day_of_week')->all();

        for ($day = 0; $day <= 6; $day++) {
            if (!in_array($day, $existing_days)) {
                $this->schedules()->create([
                    'day_of_week' => $day,
                    'open_time' => '00:00:00',
                    'close_time' => '23:59:59',
                    'is_closed' => false,
                ]);
            }
        }

        // odświeżenie relacji
        $this->unsetRelation('schedules');
        $this->load('schedules');
    }
}
