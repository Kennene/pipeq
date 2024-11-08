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
}
