<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Models\Error;
use App\Models\Color;

use App\Events\UpdateUserAboutHisTicket;

use App\Models\Ticket;
use App\Models\Destination;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $color = new Color();
        $destinations = Destination::all();

        return view('user.user')->with(compact('color', 'destinations'));
    }

    // todo: move it to seperate TicketController
    public function register(Request $request, $destination_id)
    {
        // check if specified destination exists
        if (Destination::find($destination_id) === null) {
            $error = new Error(title: 'Destination not found', http: 404);
            return $error->toHTTPresponse();
        }

        // * Instruction on how to get ticket token from session
        //todo: remove that, it's not needed just and instruction
        // $ticketToken = session('ticket_token');//->uuid;
        // dd($ticketToken->toString());

        // todo: change it to seek the token, cut off from auth
        //* check if user can have multiple tickets registered
        //! actually remove that. make user always have only one ticket
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

        //! broken functionality (for now)
        // // update display about new registered ticket
        // broadcast(new UpdateDisplayAboutTicket($ticket));

        // Store ticket token in cookie and session
        //* Ticket's token is used for "light authentication" and listening on websocket channel
        Cookie::queue('ticket_token', $ticket->token);

        // Return response with ticket token as channel to listen on 
        return response()->json([
            'message' => 'Ticket registered',
            'channel' => $ticket->token
        ], 201);
    }
}
