<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
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
        elseif (Auth::user()->role->role_abbreviation  == 'superadmin') {
            return $next($request);
        }
        elseif (Auth::user()->role->role_abbreviation  == 'beneficiary') {
            return redirect(app()->getLocale().'/beneficiary/home');
        }
        elseif (Auth::user()->role->role_abbreviation  == 'staff') {
            return redirect(app()->getLocale().'/staff/home');
        }
        elseif (in_array(Auth::user()->role->role_abbreviation, ['paralegal', 'clerk'], true)) {
            return redirect(app()->getLocale().'/clerk/home');
        }
        elseif (Auth::user()->role->role_abbreviation  == 'admin') {
            return redirect(app()->getLocale().'/admin/home');
        }
        else{
            return redirect('home')->withErrors('error',"You don't have access.");
        }
    }
}
