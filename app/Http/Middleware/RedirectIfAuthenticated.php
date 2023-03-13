<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;


class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if (Auth::guard($guard)->check()) {
            if (Auth::user()->access_level == 'Admin' || Auth::user()->access_level == 'Partner' || Auth::user()->access_level == 'Donor' || Auth::user()->access_level == 'County' || Auth::user()->access_level == 'Sub County') {
                return redirect('/admin/dashboard')->with('success', 'Login Success! You will be redirected to your Home page in a few.');
            }
            if (Auth::user()->access_level == 'Facility') {
                return redirect('/admin/dashboard')->with('success', 'Login Success! You will be redirected to your Home page in a few.');
            }
        }
        return $next($request);
    }
}
