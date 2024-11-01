<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Error;
use App\Models\Color;

use App\Events\TicketRegister;
use App\Events\TicketNew;
use App\Models\Ticket;
use App\Models\Destination;

class UserController extends Controller
{
    public function index()
    {
        $variables["color"] = new Color();
        $variables["destinations"] = Destination::all();

        return view('user.user')->with($variables);
    }

    public function register(Request $request, $destination_id)
    {
        // check if specified destination exists
        if (Destination::find($destination_id) === null) {
            $error = new Error(title: 'Destination not found', http: 404);
            return $error->toHTTPresponse();
        }

        // check if user can have multiple tickets registered
        if (!env('MULTIPLE_TICKETS')) {
            if (Ticket::isUserAlreadyRegistered(auth()->user()->id)) {
                // todo: zamiast zwracać error, dołącz do nasłuchiwania eventu zmian jego biletu
                $error = new Error('User already registered');
                return $error->toHTTPresponse();
            }
        }

        $ticket = Ticket::create([
            'user_id' => auth()->user()->id,
            'destination_id' => $destination_id,
        ]);

        broadcast(new TicketRegister(auth()->user(), $ticket));
        broadcast(new TicketNew($ticket->id));
        return response()->json(['message' => 'Ticket registered'], 201);
    }
}
