<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationsSchedule extends Model
{
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function isOpenNow()
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
}
