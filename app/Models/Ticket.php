<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Error;
use App\Models\User;

use App\Models\Status;
use App\Models\Destination;
use App\Models\Workstation;

class Ticket extends Model
{
    use HasFactory;
    protected $table = "tickets";
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int'; 

    // todo: wywalić stąd user_id. Powinien się on domyślnie uzupełniać z sesji a nie być ręcznie wprowadzany
    protected $fillable = ['user_id', 'destination_id', 'status_id', 'ticket_nr'];

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
            $ticket_nr = env('MAX_TICKET_NUMBER', 99);
            $nextId = (static::max('id') ?? 0) + 1;
            $ticket->ticket_nr = ($nextId % $ticket_nr) ?: $ticket_nr;
        });
    }

    /**
     * Check if user is already registered
     * @return bool
     */
    static function isUserAlreadyRegistered(int $user_id): bool
    {
        return Ticket::where('user_id', $user_id)->exists();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOwner()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Returns desired Destination class of the Ticket
     * @return Destination|null
     */
    public function getDestination(): ?Destination
    {
        return Destination::find($this->destination_id);
    }

    /**
     * Sets new workstation for the ticket
     * @param int $workstation_id
     * @return Error|null
     * @throws \Exception
     * @throws \Throwable
     */
    public function updateWorkstation(int $workstation_id): ?Error
    {
        $workstation = Workstation::find($workstation_id);
        if ($workstation === null) {
            return new Error(title: 'Workstation not found', http: 404);
        }

        try {
            $this->workstation_id = $workstation_id;
            $this->save();
        } catch (\Exception $errorMessage) {
            return new Error('Failed to update workstation', $errorMessage, 500);
        }

        return null;
    }

    /**
     * Sets new status for the ticket
     * @param int $status_id
     * @return Error|null
     * @throws \Exception
     * @throws \Throwable
     */
    public function updateStatus(int $status_id): ?Error
    {
        $status = Status::find($status_id);
        if ($status === null) {
            return new Error(title: 'Status not found', http: 404);
        }

        try {
            $this->status_id = $status_id;
            $this->save();
        } catch (\Exception $errorMessage) {
            return new Error('Failed to update status', $errorMessage, 500);
        }

        return null;
    }

    // todo: poprawić parametr $userId na klasę User
    /**
     * Updates information on Ticket by which whom it was modified
     * @param int $userId
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function setModifiedBy($userId): ?Error
    {
        $user = User::find($userId);
        if ($user === null) {
            return new Error(title: 'User not found', http: 404);
        }

        try {
            $this->modified_by = $userId;
            $this->save();
        } catch (\Exception $errorMessage) {
            return new Error('Failed to update status', $errorMessage, 500);
        }

        return null;
    }

    /**
     * Removes ticket from queue and moves in into tickets_ended table, for historical purposes
     * @return Error|null
     * @throws \Exception
     * @throws \Throwable
     */
    public function end(): ?Error
    {
        // try to update status
        $error = $this->updateStatus(Status::END);
        if ($error !== null) {
            return $error;
        }

        // check if corralated view for sure exists, because why not, better to be safe
        $tv = TicketView::find($this->id);
        if ($tv === null) {
            return new Error('Correlated view does not exist. Panic!', 404);
        }

        // try to insert ended ticket into tickets_ended table
        try {
            DB::table('tickets_ended')->insert([
                'original_id' => $this->id,
                'user_id' => $this->user_id,
                'ticket_nr' => $this->ticket_nr,
                'status_id' => $this->status_id,
                'destination_id' => $this->destination_id,
                'workstation_id' => $this->workstation_id,
                'original_created_at' => $this->created_at,
                'original_updated_at' => $this->updated_at,
                'modified_by' => $this->modified_by,
    
                'user' => $tv->user,
                'status' => $tv->status,
                'destination' => $tv->destination,
                'workstation' => $tv->workstation
            ]);
        } catch (\Exception $errorMessage) {
            return new Error('Failed to safe data into tickets_ended', $errorMessage, 500);
        }

        $this->delete();
        return null;
    }

    //? Do czego jest ta funkcja? Chyba nie jest nigdy używana
    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }


    /**
     * Example of a method that uses a raw SQL query
     */
    // public function example() {
    //     $database = DB::connection();
    //     $query = <<<SQL
    //         SELECT count(id) FROM tickets;
    //     SQL;
    //     $result = $database->select($query);
    //     return $result;
    // }


}