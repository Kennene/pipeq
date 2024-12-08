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

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $color = new Color();
        $destinations = Destination::all();
        $token = $this->getUserToken($request);

        return view('user.user')->with(compact('color', 'token', 'destinations'));
    }

    public function index2(Request $request): View
    {
        $color = new Color();
        $destinations = Destination::all();

        return view('subscriber.user')->with(compact('color', 'destinations'));
    }




    // todo: wywalić to. kod powtarza się w TicketController
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
