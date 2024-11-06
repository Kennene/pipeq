<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Error;
use App\Models\Color;

use App\Events\RegisterNewTicket;
use App\Events\UpdateDisplayAboutTicket;

use App\Models\Ticket;
use App\Models\Destination;

use function Symfony\Component\String\b;

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
            if (auth()->user()->hasTickets()) {
                // todo: zamiast zwracać error, dołącz do nasłuchiwania eventu zmian jego biletu
                $error = new Error('User already registered');
                return $error->toHTTPresponse();
            }
        }

        // create new ticket
        $ticket = Ticket::create([
            'destination_id' => $destination_id,
        ]);

        // broadcast event to user his ticket has been registered
        broadcast(new RegisterNewTicket($ticket));

        // update display about new registered ticket
        broadcast(new UpdateDisplayAboutTicket($ticket));

        return response()->json(['message' => 'Ticket registered'], 201);
    }
}
