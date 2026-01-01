<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Religion;
use Facade\FlareClient\Http\Response;

class ReligionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $religions = Religion::get(['id', 'religion']);

        return view('manager.religions', compact('religions'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'religion' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $religion
         * @return \App\Models\Religion
         */

        $religion = new Religion();

        $religion->religion = $request->religion;

        /**
         * Save the type to the database
         */

        $religion->save();

        /**
         *  Redirect user to Religion page
         */
        if ($religion) {

            // Log user activity
            activity()->log('Created religion');

            return redirect()->back()
                            ->with('status', 'Religion information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding religion information failed');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($locale, $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $locale, $id)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'religion' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $religion
         * @return \App\Models\Religion
         */

        $religion = Religion::findOrFail($id);

        $religion->religion = $request->religion;

        /**
         * Update the type to the database
         */

        $religion->update();

        /**
         *  Redirect user to religion page
         */
        if ($religion) {

            // Log user activity
            activity()->log('Updated religion');

            return redirect()->back()
                            ->with('status', 'Religion information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating religion information failed');
        }
    }

    /**
     * Remove the specified religion from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting religion information from the database

        $religion = Religion::findOrFail($id);

        $religion->delete();

        if($religion){

            // Log user activity
            activity()->log('Trashed religion');

            return redirect()->back()->with('status','Religion information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing religion information failed, please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
