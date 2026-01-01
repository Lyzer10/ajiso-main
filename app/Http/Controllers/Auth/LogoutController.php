<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class LogoutController extends Controller
{
    /**
     * Logout user session.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        auth()->Auth::logout();        

        /**
         * Redirect user to landing page
         */
        return redirect()->route('/');

    }
}
