<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Events\UserRegister;

class UserRegisterController extends Controller
{
    public function register($id_user)
    {
        //? Czy nie lepiej wywalić to wszystko i przenieść rejestrację usera do kontrolera od usera?
        // todo: zaimplementować logikę dodawania użytkownika do kolejki
        $ticketnumber = rand(10, 99);

        //? czy zamiast poniższego kodu nie lepiej zwrócić kodu kanału na którym będzie obsługiwany jego ticket?
            // todo: jeżeli proces rejestracji powiódł się, nadaj wiadomość na UserRegister
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
}
