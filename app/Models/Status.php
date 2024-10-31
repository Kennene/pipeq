<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    // todo: dodać statyczne pola odpowiadające za 1- waiting, 2 - wpuszczony itd...

    protected $table = 'statuses';
    protected $fillable = ['name', 'description'];

}
