<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketView extends Model
{
    use HasFactory;
    
    protected $table = 'ticket_view';
    public $timestamps = false;
}
