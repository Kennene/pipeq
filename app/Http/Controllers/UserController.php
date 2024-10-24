<?php

namespace App\Http\Controllers;
use App\Events\UserRegister;
use App\Events\UserMove;
use App\Models\Ticket;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('user.user');
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
