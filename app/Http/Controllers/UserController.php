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

        // get token from cookie within request
        $token = $request->cookie('ticket_token');

        // check if token was found in cookie
        if ($token) {
            // If user sends custom token in cookie, Laravel won't decipher it, therefore reject it
            session(['ticket_token' => $token]);
        } elseif (session()->has('ticket_token')) {
            // User may have deleted the cookie, but still have the session
            $token = session('ticket_token');
            Cookie::queue('ticket_token', $token);
        }

        // if token is set, that means user already has ticket
        if (isset($token)) {
            return response()->json([
                'message' => 'You already have a ticket registered',
                'channel' => $token
            ], 200);
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
        session(['ticket_token' => $ticket->token]);

        // Return response with ticket token as channel to listen on 
        return response()->json([
            'message' => 'Ticket registered',
            'channel' => $ticket->token
        ], 201);
    }
}
