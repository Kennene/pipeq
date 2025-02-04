<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Error;
use App\Models\Role;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, int $role): Response
    {
        // if this is a user space, move forward
        if ($role < Role::DISPLAY) {
            return $next($request);
        }

        // get loggged user and prepare Unauthorized error
        $user = Auth::user();
        $error = new Error(title: 'Unauthorized', http: RESPONSE::HTTP_UNAUTHORIZED);

        // check if user doesn't exist or doesn't have roles
        if (!$user || !$user->roles) {
            return $error->toHTTPresponse();
        }

        // grab user roles
        $user_roles = $user->roles->pluck('id')->toArray();

        /**
         * Roles are cascading:
         * 1. User
         * 10. Display
         * 20. Coordinator
         * 30. API
         * 40. Administrator
         * 
         * Each one of them are seperated by 10.
         * Example:
         *  - User can only access User
         *  - API can access User, Display, Coordinator
         *  - Administrator can access everything
         * 
         *  User can also have mutliple roles in database.
         *  ^ For now this is only for future use if infrastructure will change.
         */

        // Check for cascading roles
        foreach ($user_roles as $user_role) {
            if ($user_role >= $role) {
                return $next($request);
            }
        }

        // if user doesn't have required role, return error
        return $error->toHTTPresponse();
    }
}
