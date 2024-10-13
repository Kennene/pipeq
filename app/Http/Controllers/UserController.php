<?php

namespace App\Http\Controllers;
use App\Events\UserRegister;
use App\Events\UserEnter;;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('user.user');
    }

    public function enter()
    {
        //? Do czego to było potrzebne? Nie pamietam, to chyba smietnik
        //? Chyba do tego, żeby proces rejestracji użytkownika odbywał się w kontrolerze usera
            // Wygenerowane przez Copilota, może okazać się użyteczne
            // broadcast(new UserEnter('User entered the chat'));
            // return view('user.user');

            event(new UserEnter('Hello from Laravel!'));
    }
}
