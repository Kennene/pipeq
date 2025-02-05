<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    protected $table = 'reasons';

    protected $fillable = [
        'destination_id',
        'description',
        'is_active',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::retrieved(function ($translation) {
            $translation->description = __($translation->description);
        });
    }
}
