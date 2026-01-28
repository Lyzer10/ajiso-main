<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    protected function credentials(Request $request)
    {
        return [
            'email'     => $request->email,
            'password'  => $request->password,
            'is_active' => 1,
        ];
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Sign in an authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        /**
         * Get a validator for an incoming login request.
         *
         * @param  array  $data
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        /**
         * Make an attempt to sign in a user
         */


        if (!auth()->attempt($this->credentials($request), $request->remember)) {
            return back()->with('status', 'The provided credentials do not match our records.');
        }

        if (in_array(Auth::user()->role->role_abbreviation, ['paralegal', 'clerk'], true)
            && !Auth::user()->has_system_access) {
            Auth::logout();
            return back()->with('status', 'Your account does not have access to the system.');
        }

        /**
         *  Redirect user to respective dashboard
         */

        switch (Auth::user()->role->role_abbreviation) {
            case 'superadmin':
                session(['locale' => 'en']);
                session()->forget('locale_user_set');
                app()->setLocale('en');
                return redirect()->route('admin.super.home', 'en');
                break;

            case 'admin':
                session(['locale' => 'en']);
                session()->forget('locale_user_set');
                app()->setLocale('en');
                return redirect()->route('admin.home', 'en');
                break;

            case 'staff':
                session(['locale' => 'en']);
                session()->forget('locale_user_set');
                app()->setLocale('en');
                return redirect()->route('staff.home', 'en');
                break;

            case 'paralegal':
            case 'clerk':
                session(['locale' => 'sw']);
                session()->forget('locale_user_set');
                app()->setLocale('sw');
                return redirect()->route('clerk.home', 'sw');
                break;

            case 'beneficiary':
                session(['locale' => 'en']);
                session()->forget('locale_user_set');
                app()->setLocale('en');
                return redirect()->route('beneficiary.home', 'en');
                break;

            default:
                return redirect('login')->withErrors('error', "You don't have access.");
                break;
        }
    }
}
