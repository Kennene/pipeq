<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workstation extends Model
{
    use HasFactory;

    protected $table = 'workstations';

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
