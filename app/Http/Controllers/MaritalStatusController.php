<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaritalStatus;
use Facade\FlareClient\Http\Response;

class MaritalStatusController extends Controller
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
        $marital_statuses = MaritalStatus::get(['id', 'marital_status']);

        return view('manager.marital-statuses', compact('marital_statuses'));

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
            'marital_status' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $marital_status
         * @return \App\Models\MaritalStatus
         */

        $marital_status = new MaritalStatus();

        $marital_status->marital_status = $request->marital_status;

        /**
         * Save the type to the database
         */

        $marital_status->save();

        /**
         *  Redirect user to Marital Status page
         */
        if ($marital_status) {

            // Log user activity
            activity()->log('Created marital status');

            return redirect()->back()
                            ->with('status', 'Marital status information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding marital status information failed');
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
            'marital_status' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $marital_status
         * @return \App\Models\MaritalStatus
         */

        $marital_status = MaritalStatus::findOrFail($id);

        $marital_status->marital_status = $request->marital_status;

        /**
         * Update the type to the database
         */

        $marital_status->update();

        /**
         *  Redirect user to Marital Status page
         */
        if ($marital_status) {

            // Log user activity
            activity()->log('Updated marital status');

            return redirect()->back()
                            ->with('status', 'Marital status information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating marital status information failed');
        }
    }

    /**
     * Remove the specified marital_status from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting marital_status information from the database

        $marital_status = MaritalStatus::findOrFail($id);

        $marital_status->delete();

        if($marital_status){

            // Log user activity
            activity()->log('Trashed marital status');

            return redirect()->back()->with('status','Marital Status information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing marital status information failed, please try again.');
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
