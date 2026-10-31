<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check if the user is logged in, else redirect to index
        if (!\Auth::check()) {
            return \Redirect(route('login'));
        }

        $account = \Auth::user();

        // check if the user is an admin
        $roles = $account->getRelationValue('roles');
        if (!in_array('Admin', $roles->pluck('name')->toArray())) {
            return \Redirect(route('login'));
        }

        return $next($request);
    }
}
