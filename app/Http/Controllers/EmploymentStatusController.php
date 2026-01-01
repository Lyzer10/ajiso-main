<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmploymentStatus;
use Facade\FlareClient\Http\Response;

class EmploymentStatusController extends Controller
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
        $employment_statuses = EmploymentStatus::get(['id', 'employment_status']);

        return view('manager.employment-statuses', compact('employment_statuses'));

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
            'employment_status' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $employment_status
         * @return \App\Models\EmploymentStatus
         */

        $employment_status = new EmploymentStatus();

        $employment_status->employment_status = $request->employment_status;

        /**
         * Save the type to the database
         */

        $employment_status->save();

        /**
         *  Redirect user to Employment Status page
         */
        if ($employment_status) {

            // Log user activity
            activity()->log('Created employment status');

            return redirect()->back()
                            ->with('status', 'Employment status information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding employment status information failed');
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
            'employment_status' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $employment_status
         * @return \App\Models\EmploymentStatus
         */

        $employment_status = EmploymentStatus::findOrFail($id);

        $employment_status->employment_status = $request->employment_status;

        /**
         * Update the type to the database
         */

        $employment_status->update();

        /**
         *  Redirect user to Employment Status page
         */
        if ($employment_status) {

            // Log user activity
            activity()->log('Updated employment status');

            return redirect()->back()
                            ->with('status', 'Employment status information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating employment status information failed');
        }
    }

    /**
     * Remove the specified employment_status from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting employment_status information from the database

        $employment_status = EmploymentStatus::findOrFail($id);

        $employment_status->delete();

        if($employment_status){

            // Log user activity
            activity()->log('Trashed employment status');

            return redirect()->back()->with('status','Employment status information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing employment status information failed, please try again.');
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
