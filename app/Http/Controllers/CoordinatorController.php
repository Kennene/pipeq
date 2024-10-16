<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class CoordinatorController extends Controller
{
    public function index()
    {
        return view('coordinator.coordinator');
    }

    public function move(int $ticket_id, string $destination)
    {
        $ticket = Ticket::find($ticket_id);
        $ticket->destination = $destination;
    }
}
