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

    protected $fillable = ['user_id', 'destination_id', 'status_id'];

    protected function getId()
    {
        return $this->id;
    }

    protected function getOwner()
    {
        return $this->belongsTo(User::class);
    }

    protected function getOwnerId()
    {
        return $this->user_id;
    }

    protected function getDestination()
    {
        return $this->destination;
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