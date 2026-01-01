<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgeGroupController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
}
