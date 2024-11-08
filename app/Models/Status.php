<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    const WAITING = 1;
    const IN = 2;
    const SERVING = 3;
    const END = 4;

    protected $table = 'statuses';

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

}
