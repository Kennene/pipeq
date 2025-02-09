<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TicketView extends Model
{
    use HasFactory;

    protected $table = 'tickets_view';
    public $timestamps = false;

    // necessary for function forUser
    protected $fillable = ['user', 'ticket_nr', 'destination', 'status', 'status_id', 'workstation', 'reason'];

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
            $ticket->reason = __($ticket->reason);
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
            'reason' => $this->reason,
        ]);
    }


    /**
     * Get all the ticket history
     * 
     * @return array The ticket history
     */
    public static function getHistory(): array
    {
        $database = DB::connection();
        $query = <<<SQL
            SELECT 
                th.ticket_id,
                th.ticket_nr,
                s.name AS status,
                d.name AS destination,
                w.name AS workstation,
                th.original_created_at AS created_when,
                th.original_updated_at AS updated_when,
                th.modified_by AS updated_by

            FROM tickets_history th 
            JOIN statuses s ON th.status = s.id 
            JOIN destinations d ON th.destination = d.id
            JOIN workstations w ON th.workstation = w.id

            ORDER BY th.id DESC;
        SQL;
        $tickets_history = $database->select($query);
        return $tickets_history;
    }
}
