<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Error;
use App\Models\Color;

use App\Events\RegisterNewTicket;
use App\Events\UpdateUserAboutHisTicket;
use App\Events\UpdateDisplayAboutTicket;

use App\Models\Ticket;
use App\Models\Destination;

class UserController extends Controller
{
    public function index(): View
    {
        $variables["color"] = new Color();
        $variables["destinations"] = Destination::all();

        return view('user.user')->with($variables);
    }

    // todo: move it to seperate TicketController
    public function register(Request $request, $destination_id)
    {
        // check if specified destination exists
        if (Destination::find($destination_id) === null) {
            $error = new Error(title: 'Destination not found', http: 404);
            return $error->toHTTPresponse();
        }

        // todo: change it to seek the token, cut off from auth
        //* check if user can have multiple tickets registered
        if (!env('MULTIPLE_TICKETS')) {
            if (auth()->user()->hasTickets()) {
                // find user ticket
                $ticket = Ticket::where('user_id', auth()->user()->id)->first();

                // notify user about his already registered ticket
                broadcast(new UpdateUserAboutHisTicket($ticket, "You already have a ticket registered"));

                // return error
                $error = new Error('User already registered');
                return $error->toHTTPresponse();
            }
        }

        // create new ticket
        $ticket = Ticket::create([
            'destination_id' => $destination_id,
            'token' => Str::uuid(),
        ]);

        // broadcast event to user his ticket has been registered
        broadcast(new RegisterNewTicket($ticket));

        // update display about new registered ticket
        broadcast(new UpdateDisplayAboutTicket($ticket));

        return response()->json(['message' => 'Ticket registered'], 201);
    }
}
