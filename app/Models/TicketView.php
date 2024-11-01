<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketView extends Model
{
    use HasFactory;
    
    protected $table = 'ticket_view';
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        
        static::retrieved(function ($ticket) {
            $ticket->status = __($ticket->status);
            $ticket->destination = __($ticket->destination);
            $ticket->workstation = __($ticket->workstation);
        });
    }
}
