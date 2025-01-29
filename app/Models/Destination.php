<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function translate()
    {
        $this->name = __('destinations.' . $this->id . '.name');
        $this->description = __('destinations.' . $this->id . '.description');
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
        $now = now();
        $dayOfWeek = $now->dayOfWeek; // 0 = Sunday, 6 = Saturday
        $time = $now->format('H:i:s');

        // Check if it's open now
        if ($this->isOpenNow()) {
            return __("time-restricted.open");
        }

        // Look for the next opening day
        for ($i = 0; $i < 7; $i++) {
            $nextDay = ($dayOfWeek + $i) % 7;

            $nextSchedule = $this->schedules()
                ->where('day_of_week', $nextDay)
                ->where('is_closed', false)
                ->orderBy('open_time', 'asc')
                ->first();

            if ($nextSchedule) {
                if ($i == 0 && $nextSchedule->open_time > $time) {
                    return __('time-restricted.opens_today', ['time' => date('H:i', strtotime($nextSchedule->open_time))]);
                }

                // todo: tu jest chyba coś pochrzanione z tym -1
                return __('time-restricted.opens_on', [
                    'day' => __("day.".now()->startOfWeek()->addDays($nextDay-1)->format('w')),
                    'time' => date('H:i', strtotime($nextSchedule->open_time))
                ]);
            }
        }

        return __("time-restricted.closed-indefinitely");
    }
}
