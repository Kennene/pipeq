<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    public static $PRIMARY;
    public static $SECONDARY;
    public static $ADDITIONAL;
    public static $DETAILS;

    public static $WHITE;
    public static $BLACK;

    public static function boot()
    {
        parent::boot();

        self::$PRIMARY = config('colors.primary');
        self::$SECONDARY = config('colors.secondary');
        self::$ADDITIONAL = config('colors.additional');
        self::$DETAILS = config('colors.details');

        self::$WHITE = config('colors.white');
        self::$BLACK = config('colors.black');
    }
}