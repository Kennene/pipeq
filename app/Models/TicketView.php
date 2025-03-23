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

        /**
         * Translating this way wouldn't be the best approach,,
         * as it causes a mismatch between model attributes and database columns when saving the object.
         * Using built-in PHP getXAttribute method would be a better solution, but in this case, TicketView is a database view
         * and cannot be saved anyway — so this translation method is acceptable here.
         * If it works, don't fix it ;)
         */
        
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
        $tickets_history = DB::table('tickets_history as th')
        ->leftJoin('statuses as s', 'th.status', '=', 's.id')
        ->leftJoin('destinations as d', 'th.destination', '=', 'd.id')
        ->leftJoin('workstations as w', 'th.workstation', '=', 'w.id')
        ->select(
            'th.ticket_id',
            'th.ticket_nr',
            's.name as status',
            'd.name as destination',
            'w.name as workstation',
            'th.original_created_at as created_when',
            'th.original_updated_at as updated_when',
            'th.modified_by as updated_by'
        )
        ->orderBy('th.id', 'desc')
        ->get();

        // since it is raw SQL query, it needs to be manually translated //? can we convert it to Eloquent?
        foreach($tickets_history as $log) {
            $log->status = __($log->status);
            $log->destination = __($log->destination);
            $log->workstation = __($log->workstation);
        }

        return $tickets_history->toArray();
    }
}
