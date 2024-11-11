<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;

use App\Models\TicketView;

class DisplayController extends Controller
{
    public function index()
    {
        $color = new Color();
        $tickets = TicketView::all();

        return view('display.display')->with(compact('color', 'tickets'));
    }
}
