<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Error;
use App\Models\Color;

use App\Models\Ticket;
use App\Models\Status;
use App\Models\Workstation;

class CoordinatorController extends Controller
{
    public function index()
    {
        $variables["color"] = new Color();

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

        // try to update workstation
        $error = $ticket->updateWorkstation($workstation_id);
        if ($error !== null) {
            return $error->toHTTPresponse();
        }

        // check if status_id is provided in POST request
        if ($request->has('status_id')) {
            $status_id = $request->input('status_id');

            if(Status::find($status_id) === null) {
                $handled_error = 'Invalid status provided, defaulting to status 2';
                $status_id = 2;
            }
        } else {
            $handled_error = 'No status provided, defaulting to status 2';
            $status_id = 2;
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
        return response()->json(['message' => $handled_error ?? "Success"], 202);
    }

    public function end(int $ticket_id)
    {

    }
}
