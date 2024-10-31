<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;

use App\Models\TicketView;

class DisplayController extends Controller
{
    public function index()
    {
        $variables["color"] = new Color();
        $variables["tickets"] = TicketView::all();

        return view('display.display')->with($variables);
    }
}
