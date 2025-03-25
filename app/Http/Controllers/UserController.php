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

        // If there are no currently opened destinations, redirect user to time-restricted page
        if($opened_destinations->isEmpty()) {
            return $this->timeRestricted();
        }

        return view('user.user')->with(compact('token', 'opened_destinations', 'reasons'));
    }

    private function timeRestricted(): View
    {
        $destinations = Destination::withOpeningHours();

        $translations = [
            'locale' => app()->getLocale(),
            'title' => __('time-restricted.title'),
            'message' => __('time-restricted.message'),
            'opens' => __('time-restricted.opens'),
            'mail' => __('time-restricted.mail'),
            'sorry1' => __('time-restricted.sorry.1'),
            'sorry2' => __('time-restricted.sorry.2'),
            'company' => config('app.name', __('time-restricted.company')),
        ];

        return view('user.time-restricted')->with(compact('destinations', 'translations'));
    }
}
