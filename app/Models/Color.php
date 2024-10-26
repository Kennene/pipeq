<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    const main = "#091057";
    const secondary = "#EC8305";
    const additional = "#024CAA";
    const details = "#DBD3D3";

    const white = "#f2f2f2 ";
    const black = "#202124";

}
