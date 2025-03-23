<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Error;

trait HandlesUserToken
{
    /**
     * Get user's token from cookie or session
     * 
     * Function also corrects user's session if he has token, but no cookie and vice versa
     * @param Request|null $request If user sends self-made token in cookie, Laravel won't decipher it, therefore reject it
     * @return string|null
     * 
     */
    protected function getUserToken(?Request $request = null): ?string
    {
        $token = $request->cookie('ticket_token') ?? session('ticket_token');

        if ($token !== null) {
            $this->setUserToken($token); // ewentualnie: obsłużyć Error, jeśli chcesz
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
                http: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return null;
    }
}
