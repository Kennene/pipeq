<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Collection;

class Reason extends Model
{
    protected $table = 'reasons';

    // todo: add coordinator ability to add reasons
    protected $fillable = [
        'destination_id',
        'description',
        'is_active',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * Automatically translate description attribute when retrieving like $object->description
     */
    public function getDescriptionAttribute(): string
    {
        return __($this->getRawOriginal('description'));
    }

    public static function perDestination(): Collection
    {
        return Reason::all()
        ->filter->isActive()
        ->groupBy('destination_id');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
