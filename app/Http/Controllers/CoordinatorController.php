<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Error;
use App\Models\Color;

use App\Events\TicketUpdate;
use App\Models\TicketView;
use App\Models\TicketEnded;
use App\Models\Ticket;
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
        $variables["destinations"] = Destination::all();
        $variables["workstations"] = Workstation::all();
        
        return view('coordinator.coordinator')->with($variables);
    }

    public function move(Request $request, int $ticket_id, string $workstation_id)
    {
        // check if specified ticket exists
        $ticket = Ticket::find($ticket_id);
        if ($ticket === null) {
            $error = new Error(title: 'Ticket not found', http: 404);
            return $error->toHTTPresponse();
        }

        // check if status_id is provided in POST request
        if ($request->has('status_id')) {
            $status_id = $request->input('status_id');

            // check if status_id is valid
            if(Status::find($status_id) === null) {
                $error = new Error(title: 'Provided status does not exist', http: 404);
                return $error->toHTTPresponse();
            }

            // if provided status means end of ticket, redirect to end method
            if($status_id == Status::END) {
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

        // try to update status
        $error = $ticket->updateStatus($status_id);
        if ($error !== null) {
            return $error->toHTTPresponse();
        }

        //? czy to na pewno konieczne?
            // check if user is allowed to move ticket
            $error = $ticket->setModifiedBy(auth()->user()->id);
            if ($error !== null) {
                return $error->toHTTPresponse();
            }

        $ticket->save();
        broadcast(new TicketUpdate($ticket->id));
        return response()->json(['message' => $handled_error ?? "Success"], 200);
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
