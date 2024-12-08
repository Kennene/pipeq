<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketView extends Model
{
    use HasFactory;

    protected $table = 'tickets_view';
    public $timestamps = false;

    // necessary for function forUser
    protected $fillable = ['user', 'ticket_nr', 'destination', 'status', 'status_id', 'workstation'];

    /**
     * Translate status, destination and workstation on retrieved
     */
    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($ticket) {
            $ticket->status = __($ticket->status);
            $ticket->destination = __($ticket->destination);
            $ticket->workstation = __($ticket->workstation);
        });
    }

    /**
     * Override the save method to prevent creating new entries in database
     * @throws \Exception
     */
    public function save(array $options = [])
    {
        throw new \Exception("This is a view in database. You cannot write data to it.");
    }

    /**
     * Prepares the ticket data for user view by removing kinda sensitive fields
     * 
     * @return array The ticket data array with sensitive fields removed.
     */
    public function forUser(): TicketView
    {
        return $this->make([
            'user' => $this->user,
            'ticket_nr' => $this->ticket_nr,
            'destination' => $this->destination,
            'status' => $this->status,
            'status_id' => $this->status_id,
            'workstation' => $this->workstation,
        ]);
    }
}
