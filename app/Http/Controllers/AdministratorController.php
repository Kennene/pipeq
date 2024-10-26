<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;

class AdministratorController extends Controller
{
    public function index()
    {
        $variables["color"] = new Color();

        return view('administrator.administrator')->with($variables);
    }
}
