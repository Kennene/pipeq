<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

use App\Models\Error;
use App\Events\UpdateUserAboutHisTicket;
use App\Events\UpdateDisplayAboutTicket;
use App\Events\EndUserTicket;

use App\Models\Status;
use App\Models\Destination;
use App\Models\Ticket;


class TicketController extends Controller
{
    public function register(Request $request, $destination_id)
    {
        // check if specified destination exists
        if (Destination::find($destination_id) === null) {
            $error = new Error(title: 'Destination not found', http: 404);
            return $error->toHTTPresponse();
        }

        //! what if user mistenly selects wrong destination and wants to correct it?
        // for debugging purposes, enable multiple tickets registration
        if (!env('APP_DEBUG')) {
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

            // todo: handle exception when user somehow still has valid token, but ticket is nowhere to be found
            // if token is set, that means user already has ticket
            if (isset($token)) {
                //todo: somehow update the status of user's ticket (he may be already in the middle of the process)
                return response()->json([
                    'message' => 'You already have a ticket registered',
                    'channel' => $token
                ], 200);
            }
        }

        // create new ticket
        $ticket = Ticket::create([
            'destination_id' => $destination_id,
            'token' => Str::uuid(),
        ]);

        // update display about new registered ticket
        broadcast(new UpdateDisplayAboutTicket($ticket));

        // Store ticket token in cookie and session
        // Ticket's token is used for "light authentication" and listening on websocket channel
        Cookie::queue('ticket_token', $ticket->token);
        session(['ticket_token' => $ticket->token]);

        // Return response with ticket token as channel to listen on 
        return response()->json([
            'message' => 'Ticket registered',
            'channel' => $ticket->token
        ], 201);
    }

    public function move(Request $request, int $ticket_id, ?string $workstation_id, int $status_id = null)
    {
        // check if specified ticket exists
        $ticket = Ticket::find($ticket_id);
        if ($ticket === null) {
            $error = new Error(title: 'Ticket not found', http: 404);
            return $error->toHTTPresponse();
        }

        // check if status_id is provided via web route
        if ($status_id !== null) {

            // check if status_id is valid
            if (Status::find($status_id) === null) {
                $error = new Error(title: 'Provided status does not exist', http: 404);
                return $error->toHTTPresponse();
            }

            // if provided status means end of ticket, redirect to end method
            if ($status_id == Status::END) {
                return redirect()->to("/end/{$ticket_id}");
            }
        } else {
            $handled_error = 'No status provided, defaulting to status ' . Status::IN;
            $status_id = Status::IN;
        }

        // try to update workstation
        $error = $ticket->updateWorkstation($workstation_id);
        if ($error !== null) {
            return $error->toHTTPresponse();
        }

        // if workstation_id is provided, try to update status
        if ($workstation_id != null) {
            // try to update status
            $error = $ticket->updateStatus($status_id);
            if ($error !== null) {
                return $error->toHTTPresponse();
            }
        } else {
            // workstation_id is not provided, set status to WAITING
            // if workstation_id is null, but status is anything other that WAITING, user would not know where to go
            $error = $ticket->updateStatus(Status::WAITING);
            if ($error !== null) {
                return $error->toHTTPresponse();
            }
            $handled_error = 'Null value provided on workstation. Clearing workstation and moving user to status ' . Status::WAITING;
        }

        $ticket->save();

        // update user that his ticket has been changed
        broadcast(new UpdateUserAboutHisTicket($ticket));

        // update display about changes made in ticket
        broadcast(new UpdateDisplayAboutTicket($ticket));

        // for debugging purposes, return whole summary of ticket
        if(env('APP_DEBUG')) {
            return response()->json(['message' => $ticket->summary() ], 200);
        }
        
        return response()->json(['message' => $handled_error ?? 'Success' ], 200);
    }

    public function end(Request $request, int $ticket_id)
    {
        // check if specified ticket exists
        $ticket = Ticket::find($ticket_id);
        if ($ticket === null) {
            $error = new Error(title: 'Ticket not found', http: 404);
            return $error->toHTTPresponse();
        }

        // try to update status
        $error = $ticket->updateStatus(Status::END);
        if ($error !== null) {
            return $error->toHTTPresponse();
        }

        // update user that his ticket has been changed
        broadcast(new EndUserTicket($ticket));

        // update display about changes made in ticket
        broadcast(new UpdateDisplayAboutTicket($ticket));

        // try to end ticket, and if that fails, return error
        $error = $ticket->end();
        if ($error !== null) {
            return $error->toHTTPresponse();
        }

        return response()->json(['message' => "Success"], 200);
    }

    public function clear(Request $request)
    {
        $message = '';

        // try to remove ticket token from session
        try {
            if (session()->has('ticket_token')) {
                Session::forget('ticket_token');
                $message .= 'Ticket token removed from session';
            }
        } catch (\Exception $e) {
            $error = new Error(title: 'Failed to clear token from session',
            description: $e->getMessage(),
            http: 500);
            return $error->toHTTPresponse();
        }

        // try to remove ticket token from cookie
        try {
            if ($request->cookie('ticket_token')) {
                Cookie::queue(Cookie::forget('ticket_token'));

                // if token was previously found in cookie, add additional message
                if ($message == '') {
                    $message .= 'Ticket token removed from cookie';
                } else {
                    $message = 'Ticket token removed from both session and cookie';
                }
            }
        } catch (\Exception $e) {
            $error = new Error(
                title: 'Failed to remove cookie with ticket token', 
                description: $e->getMessage(), 
                http: 500
            );
            return $error->toHTTPresponse();
        }

        // if no token was found, return different message
        if ($message == '') {
            $message = 'No ticket token found';
        }

        return response()->json(['message' => $message], 200);
    }
}
