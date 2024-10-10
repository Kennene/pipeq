<?php

namespace App\Http\Controllers;
use App\Events\UserRegistered;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        broadcast(new UserRegistered());
        return view('user.user');
    }
}
