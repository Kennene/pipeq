<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        $error = $ticket->updateStatus($status_id);
        if ($error !== null) {
            return response()->json(['error' => $error->title], $error->http);
        }

        $error = $ticket->setModifiedBy(auth()->user()->id);
        if ($error !== null) {
            return response()->json(['error' => $error->title], $error->http);
        }

        $ticket->save();
        return response()->json(['message' => 'Success'], 200);
    }
}
