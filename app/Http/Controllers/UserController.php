<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use App\Events\UserRegister;
use App\Events\UserMove;
use App\Models\Ticket;
use App\Models\Destination;

class UserController extends Controller
{
    public function index()
    {
        $variables["color"] = new Color();

        return view('user.user')->with($variables);
    }

    public function register($destination_id)
    {
        $destination = Destination::find($destination_id);
        if ($destination === null) {
            return response()->json(['error' => 'Destination not found'], 404);
        }

        if (Ticket::isUserAlreadyRegistered(auth()->user()->id && !env('APP_DEBUG'))) {
            return response()->json(['error' => 'User already registered'], 400);
        }

        $ticket = Ticket::create([
            'user_id' => auth()->user()->id,
            'destination_id' => $destination_id,
        ]);
        broadcast(new UserRegister($ticket));
        return response()->json(['message' => 'Success'], 200);
    }
}
