<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // dd($role);

        // $user = Auth::user();

        // if (!$user || !$user->roles) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        // $userRoles = $user->roles->pluck('id')->toArray();

        // // Check for cascading roles
        // foreach ($userRoles as $userRole) {
        //     if ($userRole >= $role) {
        //         return $next($request);
        //     }
        // }

        // return response()->json(['error' => 'Unauthorized'], 403);


        return $next($request);
    }
}
