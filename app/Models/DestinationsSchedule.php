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
