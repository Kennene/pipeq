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

    public function move(int $ticket_id, string $status)
    {
        $ticket = Ticket::find($ticket_id);
        $ticket->setStatus($status);
        $ticket->setModifiedBy(auth()->user()->id);
        $ticket->save();

        //todo: jeżeli proces przenoszenia się nie powiódł, zwróć odpowiedni kod błędu
        return 200;
    }
}
