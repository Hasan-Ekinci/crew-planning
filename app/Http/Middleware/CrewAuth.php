<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CrewAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $account = \Auth::user();
        $roles = $account->getRelationValue('roles');

        if (in_array('Admin', $roles->pluck('name')->toArray())) {
            return \Redirect(route('login'));
        }

        return $next($request);
    }
}
