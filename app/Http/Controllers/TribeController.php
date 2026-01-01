<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tribe;
use Facade\FlareClient\Http\Response;

class TribeController extends Controller
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
        $tribes = Tribe::select(['id', 'tribe'])->paginate(10);

        return view('manager.tribes', compact('tribes'));

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
            'tribe' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $tribe
         * @return \App\Models\Tribe
         */

        $tribe = new Tribe();

        $tribe->tribe = $request->tribe;

        /**
         * Save the type to the database
         */

        $tribe->save();

        /**
         *  Redirect user to Tribe page
         */
        if ($tribe) {

            // Log user activity
            activity()->log('Created tribe');

            return redirect()->back()
                            ->with('status', 'Tribe information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding tribe information failed');
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
            'tribe' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $tribe
         * @return \App\Models\Tribe
         */

        $tribe = Tribe::findOrFail($id);

        $tribe->tribe = $request->tribe;

        /**
         * Update the type to the database
         */

        $tribe->update();

        /**
         *  Redirect user to tribe page
         */
        if ($tribe) {

            // Log user activity
            activity()->log('Updated tribe');

            return redirect()->back()
                            ->with('status', 'Tribe information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating tribe information failed');
        }
    }

    /**
     * Remove the specified tribe from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting tribe information from the database

        $tribe = Tribe::findOrFail($id);

        $tribe->delete();

        if($tribe){

            // Log user activity
            activity()->log('Trashed tribe');

            return redirect()->back()->with('status','Tribe information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing tribe information failed, please try again.');
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
