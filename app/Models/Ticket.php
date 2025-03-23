<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Error;

use App\Models\Status;
use App\Models\Destination;
use App\Models\Workstation;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use HasFactory;
    protected $table = "tickets";
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['destination_id', 'token','reason_id','locale'];

    /**
     * Boot function to add ticket_nr to the ticket
     * 
     * This function adds ticket_nr to a ticket. This ticket_nr is given
     * to a user for his information only.
     * It is not to be mistaken with the id of the ticket, which is unique
     * and used for all other purposes, during handling a ticket.
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ticket) {
            // adds ticket_nr to the ticket
            $tickets_sequence = DB::table('sqlite_sequence')
                                    ->where('name', 'tickets')
                                    ->value('seq');

            $ticket->ticket_nr = $tickets_sequence % config('pipeq.max_ticket_number') + 1;
        });

        static::updating(function ($ticket) {
            $ticket->modified_by = Auth::id();
        });
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function workstation()
    {
        return $this->belongsTo(Workstation::class);
    }

    /**
     * Returns ticket by provided token
     * 
     * @param string|null $token
     * @return Error|Ticket
     */
    public static function getByToken(?string $token): Error|Ticket
    {
        if ($token === null) {
            return new Error(title: 'No token provided', http: RESPONSE::HTTP_BAD_REQUEST);
        }

        $ticket = self::where('token', $token)->first();
        if ($ticket === null) {
            return new Error(title: 'Ticket not found', http: RESPONSE::HTTP_NOT_FOUND);
        }

        return $ticket;
    }

    /**
     * Updates destination for the ticket
     * 
     * If destination is changed, the ticket must be returned to the queue.
     * @param int $destination_id
     * @return Error|null
     * @throws \Exception
     */
    public function setDestination(int $destination_id): ?Error
    {
        // try to update destination and return ticket to the queue
        try {
            $this->destination_id = $destination_id;
            $this->returnToQueue();
        } catch (\Exception $errorMessage) {
            // dd($errorMessage);
            return new Error('Failed to update destination', $errorMessage, RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
        }

        return null;
    }

    /**
     * Sets new workstation for the ticket
     * 
     * @param int|null $workstation_id Workstation can actually be null, because coordinator might mistakenly assign ticket to a first workstation, realize mistake, and put ticket back into queue to first workstation with  waiting status.
     * @return Error|null
     * @throws \Exception
     * @throws \Throwable
     */
    public function setWorkstation(?int $workstation_id): ?Error
    {
        // check if workstation exists if it is not null
        if ($workstation_id != null) {
            $workstation = Workstation::find($workstation_id);
            if ($workstation === null) {
                return new Error(title: 'Workstation not found', http: RESPONSE::HTTP_NOT_FOUND);
            }
        }

        // try to update workstation
        try {
            $this->workstation_id = $workstation_id;
        } catch (\Exception $errorMessage) {
            return new Error('Failed to update workstation', $errorMessage, RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
        }

        return null;
    }

    /**
     * Returns ticket back to the queue
     * 
     * This function is used when a key component of the ticket has been changed.
     * Resetting the workstation and status ensures that the user is aware of the
     * his current state in the queue. Otherwise user could be very confused what he should do.
     * @return Error|null
     */
    public function returnToQueue(): ?Error
    {
        try {
            $this->workstation_id = null;
            $this->status_id = Status::WAITING;
        } catch (\Exception $errorMessage) {
            return new Error('Failed to return ticket to queue', $errorMessage, RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
        }

        return null;
    }

    /**
     * Cool code that agreagates workstations with the least amount of tickets.
     * Leaving it here for potential future reference.
     * public function setLeastBusyWorkstation(): ?Error
     * {
     *     $results = Workstation::select('workstations.id', DB::raw('count(tickets.id) as tickets'))
     *         ->leftJoin('tickets', 'workstations.id', '=', 'tickets.workstation_id')
     *         ->where('workstations.destination_id', 1)
     *         ->groupBy('workstations.id')
     *         ->orderBy('tickets', 'asc')
     *         ->get();
     */

    /**
     * Sets new status for the ticket
     * @param int $status_id
     * @return Error|null
     * @throws \Exception
     * @throws \Throwable
     */
    public function setStatus(int $status_id): ?Error
    {
        $status = Status::find($status_id);
        if ($status === null) {
            return new Error(title: 'Status not found', http: RESPONSE::HTTP_NOT_FOUND);
        }

        try {
            $this->status_id = $status_id;
        } catch (\Exception $errorMessage) {
            return new Error('Failed to update status', $errorMessage, RESPONSE::HTTP_INTERNAL_SERVER_ERROR);
        }

        return null;
    }

    /**
     * Returns a summary of the current status of the ticket
     * 
     * @return string
     * @depracated 
     */
    public function summary(): string
    {
        $message = [
            "Ticket id: {$this->id}.",
            "User {$this->ticket_nr} going to destination id {$this->destination->id}.",
            "Currently set to workstation {$this->workstation?->id} with status {$this->status->id}."
        ];

        return implode(' ', $message);
    }

    /**
     * Raw dogging database with SQL
     * 
     * public function example() {
     *    $database = DB::connection();
     *    $query = <<<SQL
     *        SELECT count(id) FROM tickets;
     *    SQL;
     *    $result = $database->select($query);
     *    return $result;
     * }
     */
}
