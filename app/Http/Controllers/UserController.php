<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Color;
use App\Models\Error;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

use App\Models\Destination;
use App\Models\Reason;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $color = new Color();
        $token = $this->getUserToken($request);

        //? Czy modele przy funkcji boot nie mogłyby się same tłumaczyć?


        //! spaghetti code, poprawić to kiedyś.
        //? Wystarczy jedna instancja Destination, ponieważ w Eloquent collection zawiera w sobie wszystkie submodele
            $all_destinations = Destination::all();
            $available_destinations = [];

            // collect all destinations that are open right now
            foreach ($all_destinations as $destination) {
                if($destination -> isOpenNow()) {
                    $available_destinations[] = $destination;
                }
            }

            // check if there are any open destinations
            if(empty($available_destinations)) {
                // if there are no available destinations, get the next opening time and return info about it
                $opens = $all_destinations->first()->getNextOpeningInfo();
                return view('user.time-restricted')->with(compact('opens'));
            }
            
            // convert array to collection
            $destinations = collect($available_destinations);

            // Translate all destinations and reasons within
            $destinations = $destinations->map(function($destination) {
                $destination->translate();
                return $destination;
            });

            // Collect all reasons in destinations to new array: destination_id => [reasons]
            foreach($destinations as $destination) {
                $reasons_in_destination = $destination->reasons->toArray();
                foreach($reasons_in_destination as $reason) {
                    if($reason["is_active"]) {
                        $reasons[$destination->id][] = $reason;
                    }
                }
            }
       
        return view('user.user')->with(compact('color', 'token', 'destinations', 'reasons'));
    }
    
    // todo: wywalić to. kod powtarza się w TicketController - wziąć te metody stamtąd
    protected function getUserToken(?Request $request = null): ?string
    {
        // try to get token from cookie or session
        $token = $request->cookie('ticket_token') ?? session('ticket_token');

        // if token is found, fix user's storage and return token
        if ($token !== null) {
            $this->setUserToken($token); //! unhandled Error
            return $token;
        }

        return null;
    }

    /**
     * Fix user's storage by overwriting ticket_token in both session and cookie
     * 
     * @param string $token
     * @return Error|null
     * @throws \Exception
     */
    protected function setUserToken(string $token): ?Error
    {
        try {
            Cookie::queue('ticket_token', $token);
            session(['ticket_token' => $token]);
        } catch (\Exception $e) {
            return new Error(
                title: 'Failed to store token in session or cookie',
                description: $e->getMessage(),
                http: RESPONSE::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return null;
    }
}
