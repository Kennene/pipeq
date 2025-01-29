<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Status;

class Color extends Model
{
    use HasFactory;
    
    public static $PRIMARY;
    public static $SECONDARY;

    public static $ACCENT1;
    public static $ACCENT2;
    public static $ACCENT3;

    public static $WHITE;
    public static $BLACK;


    public static function boot()
    {
        parent::boot();

        self::$PRIMARY = config('colors.primary');
        self::$SECONDARY = config('colors.secondary');

        self::$ACCENT1 = config('colors.accent1');
        self::$ACCENT2 = config('colors.accent2');
        self::$ACCENT3 = config('colors.accent3');

        self::$WHITE = config('colors.white', 'white');
        self::$BLACK = config('colors.black', 'black');
    }

    public static function getByStatus(Status $status)
    {
        self::boot();

        return match ($status->id) {
            Status::WAITING => self::$ACCENT3,
            Status::IN => self::$ACCENT1,
            Status::SERVING => self::$ACCENT1,
            Status::END => self::$ACCENT2,
        };
    }
}