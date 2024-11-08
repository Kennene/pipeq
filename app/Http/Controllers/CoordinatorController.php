<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Error;
use App\Models\Color;

use App\Events\UpdateDisplayAboutTicket;
use App\Events\UpdateUserAboutHisTicket;

use App\Models\Ticket;
use App\Models\TicketView;
use App\Models\Status;
use App\Models\Destination;
use App\Models\Workstation;

class CoordinatorController extends Controller
{
    public function index()
    {
        $variables["color"] = new Color();
        $variables["tickets"] = TicketView::all();
        $variables["statuses"] = Status::all();
        $variables["destinations"] = Destination::with('workstations')->get();
        $variables["workstations"] = Workstation::all();
        $variables["translations"] = [
            'statuses' => [
                '1' => __('statuses.1.name'),
                '2' => __('statuses.2.name'),
                '3' => __('statuses.3.name'),
                '4' => __('statuses.4.name'),
            ]
        ];

        return view('coordinator.coordinator')->with($variables);
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
            //! if workstation_id is null, but status is anything other that WAITING, user would not know where to go
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

    public function end(int $ticket_id)
    {
        // check if specified ticket exists
        $ticket = Ticket::find($ticket_id);
        if ($ticket === null) {
            $error = new Error(title: 'Ticket not found', http: 404);
            return $error->toHTTPresponse();
        }

        // try to end ticket, and if that fails, return error
        $error = $ticket->end();
        if ($error !== null) {
            return $error->toHTTPresponse();
        }

        return response()->json(['message' => "Success"], 200);
    }
}
