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
        $locale = $request->segment(1);
        $allowedLocales = ['en', 'sw'];
        $user = $request->user();
        $roleAbbreviation = $user ? optional($user->role)->role_abbreviation : null;
        $explicitSwitch = $request->query('set_lang') === '1';

        if ($explicitSwitch && $locale && in_array($locale, $allowedLocales, true)) {
            session(['locale_user_set' => true]);
        }

        if ($roleAbbreviation === 'paralegal') {
            $userChosenLocale = session('locale_user_set', false);
            if (!$userChosenLocale && $locale !== 'sw') {
                if (!$request->isMethod('get') && !$request->isMethod('head')) {
                    session(['locale' => 'sw']);
                    app()->setLocale('sw');
                    return $next($request);
                }
                $segments = $request->segments();
                if (!empty($segments) && in_array($segments[0], $allowedLocales, true)) {
                    $segments[0] = 'sw';
                } else {
                    array_unshift($segments, 'sw');
                }
                $redirectUrl = '/' . implode('/', $segments);
                $queryString = $request->getQueryString();
                if ($queryString) {
                    $redirectUrl .= '?' . $queryString;
                }
                return redirect()->to($redirectUrl);
            }
        }

        if ($locale && in_array($locale, $allowedLocales, true)) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        } elseif (session()->has('locale')) {
            app()->setLocale(session()->get('locale'));
        } else {
            $defaultLocale = $roleAbbreviation === 'paralegal' ? 'sw' : 'en';
            session(['locale' => $defaultLocale]);
            app()->setLocale($defaultLocale);
        }

        return $next($request);
    }
}
