<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;

class DisplayController extends Controller
{
    public function index()
    {
        $variables["color"] = new Color();

        return view('display.display')->with($variables);
    }
}
