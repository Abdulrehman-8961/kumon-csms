<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\Auth;

class AdminOrStaffMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            abort(401);
        }

        if (!in_array(Auth::user()->role, ['admin', 'staff'])) {
            abort(403, 'You do not have access.');
        }

        return $next($request);
    }
}
