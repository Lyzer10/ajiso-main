<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
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

        if (session()->has('locale')) {
            app()->setLocale(session()->get('locale'));
            //app()->setLocale($request->segment(1));

        }else{
            app()->setLocale($request->segment(1));
        }

        return $next($request);
    }
}
