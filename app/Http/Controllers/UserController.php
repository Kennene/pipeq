<?php

namespace App\Http\Controllers;
use App\Events\UserRegister;
use App\Events\UserEnter;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('user.user');
    }

    public function register($id_user)
    {
        // todo: zaimplementować logikę dodawania użytkownika do kolejki
        $ticketnumber = rand(10, 99);

        //? czy zamiast poniższego kodu nie lepiej zwrócić kodu kanału na którym będzie obsługiwany jego ticket?
        //? nie, ponieważ komunikacja server -> user realizowana jest przez broadcasty, a nie przez zapytania HTTP. Ta komunikacja odbywa się porpzez funkcję broadcast()
        // todo: jeżeli proces rejestracji powiódł się poprawnie, przy okazji zwróć kod błędu 200
        broadcast(new UserRegister($ticketnumber));
        return "200";

        // todo: jeżeli się nie powiódł, zwróć odpowiedni kod błędu
        return "500";
        /* 
            500 - Internal Server Error
            501 - Not Implemented
            502 - Bad Gateway
            503 - Service Unavailable
            504 - Gateway Timeout
        */

    }

    public function enter()
    {
        // todo: zaimplementować logikę przesuwania usera do kolejnego etapu, tudzież wpuszczania do pokoju
            // Wygenerowane przez Copilota, może okazać się użyteczne
            // broadcast(new UserEnter('User entered the chat'));
            // return view('user.user');

            event(new UserEnter('Hello from Laravel!'));
    }
}
