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
     * Translate all statuses to App locale
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function allTranslated()
    {
        $statuses = self::all();

        $translation = $statuses->map(function ($status) {
            $status->name = __($status->name);
            $status->description = __($status->description);
            return $status;
        });

        return $translation;
    }
}
