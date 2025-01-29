<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDestinationTimeAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $destination = $request->route('destination');

        dd($destination);

        if (!$destination->isOpenNow()) {
            return view('user.time-restricted');
        }

        return $next($request);
    }
}
