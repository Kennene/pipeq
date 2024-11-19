<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use \Illuminate\Http\JsonResponse;
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
    /**
     * Register new ticket
     * 
     * @param Request $request
     * @param int $destination_id
     * @return JsonResponse
     */
    public function register(Request $request, $destination_id): JsonResponse
    {
        // check if specified destination exists
        if (Destination::find($destination_id) === null) {
            $error = new Error(title: 'Destination not found', http: RESPONSE::HTTP_NOT_FOUND);
            return $error->toHTTPresponse();
        }

        // for debugging purposes, enable multiple tickets registration
        if (!env('APP_DEBUG')) {
            $token = $this->getUserToken($request);

            if ($token !== null) {
                // user registers new ticket, but he already has one
                if (Ticket::where('token', $token)->exists()) {
                    return response()->json([
                        'message' => 'You already have a ticket registered',
                        'channel' => $token
                    ], 200);
                } else {
                    // somehow user sent valid token, but ticket is not found
                    // clear user's storage and continue creating new ticket
                    $this->clearStorage($request);
                }
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
        ], RESPONSE::HTTP_CREATED);
    }

    /**
     * Get status of user's ticket
     * 
     * @param Request $request
     * @param string|null $ticket_token
     * @return JsonResponse
     */
    public function status(Request $request, ?string $ticket_token = null): JsonResponse
    {
        // use provided token or try to get it from user's storage
        $token = $ticket_token ?? $this->getUserToken($request);

        // search for ticket with provided token
        $ticket = Ticket::getByToken($token);

        // if no ticket is found, return error
        if ($ticket instanceof Error) {
            return $ticket->toHTTPresponse();
        }

        // update user about status of his ticket
        broadcast(new UpdateUserAboutHisTicket($ticket, 'Your ticket status has been requested'));

        return response()->json(['message' => 'Status sent via WebSocket'], RESPONSE::HTTP_OK);
    }

    /**
     * Provide user with his channel name
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getChannel(Request $request): JsonResponse
    {
        // try to get token from user's storage
        $token = $this->getUserToken($request);

        // search for ticket with provided token
        $ticket = Ticket::getByToken($token);

        // if no ticket is found, return error
        if ($ticket instanceof Error) {
            return response()->json(['message' => 'Channel does not exist'], RESPONSE::HTTP_NOT_FOUND);
        } else {
            // don't need, just to be sure
            $this->fixUserStorage($token);
            return response()->json(['channel' => $token], RESPONSE::HTTP_OK);
        }
    }

    /**
     * Move ticket to another workstation with specified status
     * 
     * @param Request $request
     * @param int $ticket_id
     * @param string|null $workstation_id If provided with null value, status will be set to WAITING
     * @param int|null $status_id If provided with null value, status will default to IN
     * @return JsonResponse
     */
    public function move(Request $request, int $ticket_id, ?string $workstation_id, ?int $status_id = null): JsonResponse
    {
        // check if specified ticket exists
        $ticket = Ticket::find($ticket_id);
        if ($ticket === null) {
            $error = new Error(title: 'Ticket not found', http: RESPONSE::HTTP_NOT_FOUND);
            return $error->toHTTPresponse();
        }

        // check if status_id is provided via web route
        if ($status_id !== null) {

            // check if status_id is valid
            if (Status::find($status_id) === null) {
                $error = new Error(title: 'Provided status does not exist', http: RESPONSE::HTTP_NOT_FOUND);
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
        $error = $ticket->setWorkstation($workstation_id);
        if ($error !== null) {
            return $error->toHTTPresponse();
        }

        // if workstation_id is provided, try to update status
        if ($workstation_id != null) {
            // try to update status
            $error = $ticket->setStatus($status_id);
            if ($error !== null) {
                return $error->toHTTPresponse();
            }
        } else {
            // workstation_id is not provided, set status to WAITING
            // if workstation_id is null, but status is anything other that WAITING, user would not know where to go
            $error = $ticket->setStatus(Status::WAITING);
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
        if (env('APP_DEBUG')) {
            return response()->json(['message' => $ticket->summary()], RESPONSE::HTTP_OK);
        }

        return response()->json(['message' => $handled_error ?? 'Success'], RESPONSE::HTTP_OK);
    }

    /**
     * Ends lifecycle of ticket
     * 
     * This method can be directly called from coordinator or indirectly from user
     * 
     * @param Request $request
     * @param int $ticket_id
     * @return JsonResponse
     */
    public function end(Request $request, int $ticket_id): JsonResponse
    {
        // check if specified ticket exists
        $ticket = Ticket::find($ticket_id);
        if ($ticket === null) {
            $error = new Error(title: 'Ticket not found', http: RESPONSE::HTTP_NOT_FOUND);
            return $error->toHTTPresponse();
        }

        // try to update status
        $error = $ticket->setStatus(Status::END);
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

        return response()->json(['message' => "Ticket successfully deleted"], RESPONSE::HTTP_OK);
    }

    /**
     * Ends lifecycle of ticket by user
     * 
     * This method is used when user demands to end his ticket, for example
     * if he made a mistake when choosing destination. Method clears user's
     * storage and the calls end method
     * 
     * @param Request $request
     * @param string|null $ticket_token If not provided, method will attempt to get it from user's storage
     * @return JsonResponse
     */
    public function endByUser(Request $request, ?string $ticket_token = null): JsonResponse
    {
        // use provided token or try to get it from user's storage
        $token = $ticket_token ?? $this->getUserToken($request);

        // search for ticket with provided token
        $ticket = Ticket::getByToken($token);

        // if no ticket is found, return error
        if ($ticket instanceof Error) {
            return $ticket->toHTTPresponse();
        }

        // already clear user's storage. the same is probably done in javascript, but it's better to be sure
        $this->clearStorage($request);

        // end user's ticket
        return $this->end($request, $ticket->id);
    }

    /**
     * Clear user's storage, which means removing ticket token from session and cookie
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function clearStorage(Request $request): JsonResponse
    {
        $message = '';

        // try to remove ticket token from session
        try {
            if (session()->has('ticket_token')) {
                Session::forget('ticket_token');
                $message .= 'Ticket token removed from session';
            }
        } catch (\Exception $e) {
            $error = new Error(
                title: 'Failed to clear token from session',
                description: $e->getMessage(),
                http: RESPONSE::HTTP_INTERNAL_SERVER_ERROR
            );
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
                http: RESPONSE::HTTP_INTERNAL_SERVER_ERROR
            );
            return $error->toHTTPresponse();
        }

        // if no token was found, return different message
        if ($message == '') {
            $message = 'No ticket token found';
        }

        return response()->json(['message' => $message], RESPONSE::HTTP_OK);
    }

    /**
     * Get user's token from cookie or session
     * 
     * Function also corrects user's session if he has token, but no cookie and vice versa
     * @param Request|null $request If user sends self-made token in cookie, Laravel won't decipher it, therefore reject it
     * @return string|null
     * 
     */
    private function getUserToken(?Request $request = null): ?string
    {
        // try to get token from cookie or session
        $token = $request->cookie('ticket_token') ?? session('ticket_token');

        // if token is found, fix user's storage and return token
        if ($token !== null) {
            $this->fixUserStorage($token);
            return $token;
        }

        return null;
    }

    /**
     * Fix user's storage by overwriting ticket_token in both session and cookie
     * 
     * @param string $token
     * @return Error|null
     * @throws \Exception
     */
    private function fixUserStorage(string $token): ?Error
    {
        try {
            Cookie::queue('ticket_token', $token);
            session(['ticket_token' => $token]);
        } catch (\Exception $e) {
            return new Error(
                title: 'Failed to store token in session or cookie',
                description: $e->getMessage(),
                http: RESPONSE::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return null;
    }
}
