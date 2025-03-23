<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Traits\HandlesUserToken;

use App\Models\Destination;
use App\Models\Reason;

class UserController extends Controller
{
    use HandlesUserToken;
    
    public function index(Request $request): View
    {
        $token = $this->getUserToken($request);
        $reasons = Reason::perDestination();
        $opened_destinations = Destination::openedNow();


        // Check if there are any open destinations
        if($opened_destinations->isEmpty()) {
            // if there are no currently opened destinations, redirect user to time-restricted page
            // todo: time-restricted handle soonest opening
            return view('user.time-restricted');
        }

        return view('user.user')->with(compact('token', 'opened_destinations', 'reasons'));
    }
}
