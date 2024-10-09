<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoordinatorController extends Controller
{
    public function index()
    {
        return view('coordinator.coordinator');
    }
}
