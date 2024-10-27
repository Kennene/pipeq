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

    public function register($destination_id)
    {
        $ticket = Ticket::create([
            'user_id' => auth()->user()->id,
            'destination_id' => $destination_id,
        ]);

        broadcast(new UserRegister($ticket));
        //broadcast(new UserMove($ticket));

        return 200;

        // todo: jeżeli proces dodawania się nie powiódł, zwróć odpowiedni kod błędu, na przykłąd jeżeli uzytkownik juz oczekuje
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
