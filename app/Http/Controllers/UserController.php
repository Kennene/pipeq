<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use App\Events\UserRegister;
use App\Events\UserMove;
use App\Models\Ticket;

class UserController extends Controller
{
    public function index()
    {
        $variables["color"] = new Color();

        return view('user.user')->with($variables);
    }

    public function register($destination)
    {
        $ticket = Ticket::create([
            'user_id' => auth()->user()->id,
            'destination' => $destination,
        ]);

        broadcast(new UserRegister($ticket));
        return 200;

        // todo: jeżeli proces dodawania się nie powiódł, zwróć odpowiedni kod błędu
        return 500;
        /* 
            500 - Internal Server Error
            501 - Not Implemented
            502 - Bad Gateway
            503 - Service Unavailable
            504 - Gateway Timeout
        */

    }
}
