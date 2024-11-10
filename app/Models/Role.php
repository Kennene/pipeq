<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    const USER = 1;
    const DISPLAY = 10;
    const COORDINATOR = 20;
    const API = 30;
    const ADMINISTRATOR = 40;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
