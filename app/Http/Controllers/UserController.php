<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Error;
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
        // check if specified destination exists
        if (Destination::find($destination_id) === null) {
            $error = new Error(title: 'Destination not found', http: 404);
            return $error->toHTTPresponse();
        }

        // check if user is already registered
        if (Ticket::isUserAlreadyRegistered(auth()->user()->id && env('APP_DEBUG'))) {
            $error = new Error('User already registered');
            return $error->toHTTPresponse();
        }

        $ticket = Ticket::create([
            'user_id' => auth()->user()->id,
            'destination_id' => $destination_id,
        ]);


        //broadcast(new UserRegister($ticket));
        $message = "Twój bilet w kolejce to: " . $ticket->id % 100;
        broadcast(new UserRegister(auth()->user(), $message));
        
        return response()->json(['message' => 'Success'], 200);
    }
}
