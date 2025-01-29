<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $table = 'colors';
    protected $fillable = ['name', 'hex_code'];

    public function status() {
        return $this->hasOne(Status::class);
    }
}