<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Clerk
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('home');
        }
        elseif (Auth::user()->role->role_abbreviation  == 'admin') {
            return redirect(app()->getLocale().'/admin/home');
        }
        elseif (Auth::user()->role->role_abbreviation  == 'superadmin') {
            return redirect(app()->getLocale().'/admin/super/home');
        }
        elseif (Auth::user()->role->role_abbreviation  == 'staff') {
            return redirect(app()->getLocale().'/staff/home');
        }
        elseif (Auth::user()->role->role_abbreviation  == 'paralegal') {
            return $next($request);
        }
        elseif (Auth::user()->role->role_abbreviation  == 'beneficiary') {
            return redirect(app()->getLocale().'/beneficiary/home');
        }
        else{
            return redirect('home')->withErrors('error',"You don't have access.");
        }
    }
}
