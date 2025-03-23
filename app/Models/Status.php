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

    public function color() {
        return $this->belongsTo(Color::class);
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
}
