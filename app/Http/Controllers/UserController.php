<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Color;

use App\Models\Destination;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $color = new Color();
        $destinations = Destination::all();

        return view('user.user')->with(compact('color', 'destinations'));
    }
}
