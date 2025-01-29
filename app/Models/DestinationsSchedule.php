<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationsSchedule extends Model
{
    protected $table = 'destinations_schedules';

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
