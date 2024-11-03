<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workstation extends Model
{
    use HasFactory;

    protected $table = 'workstations';
    protected $fillable = ['name', 'description'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
