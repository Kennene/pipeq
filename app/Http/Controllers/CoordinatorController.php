<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Error;
use App\Models\Color;

use App\Models\Ticket;

class CoordinatorController extends Controller
{
    public function index()
    {
        $variables["color"] = new Color();

        return view('coordinator.coordinator')->with($variables);
    }

    public function move(int $ticket_id, string $status_id)
    {
        $ticket = Ticket::find($ticket_id);
        if ($ticket === null) {
            $error = new Error(title: 'Ticket not found', http: 404);
            return $error->toHTTPresponse();
        }

        $error = $ticket->updateStatus($status_id);
        if ($error !== null) {
            return $error->toHTTPresponse();
        }

        $error = $ticket->setModifiedBy(auth()->user()->id);
        if ($error !== null) {
            return $error->toHTTPresponse();
        }

        $ticket->save();
        return response()->json(['message' => 'Success'], 200);
    }
}
