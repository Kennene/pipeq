<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use HasFactory;
    protected $table = "tickets";
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int'; 

    protected $fillable = ['user_id', 'destination', 'status'];

    protected $attributes = [
        // Default values
        'status' => null,
    ];

    protected function getId()
    {
        return $this->id;
    }

    protected function getOwner()
    {
        return $this->user_id;
    }

    protected function getDestination()
    {
        return $this->destination;
    }

    protected function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->save();
    }

    public function setModifiedBy($userId)
    {
        $this->modified_by = $userId;
        $this->save();
    }

    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }






    public function getNumberOfAwaitingTickets()
    {
        return Ticket::where('status', null)->count();
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