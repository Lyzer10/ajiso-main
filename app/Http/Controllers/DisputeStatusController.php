<?php

namespace App\Http\Controllers;

use App\Models\DisputeStatus;
use Illuminate\Http\Request;
use Facade\FlareClient\Http\Response;

class DisputeStatusController extends Controller
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
        $dispute_statuses = DisputeStatus::get(['id', 'dispute_status']);

        return view('manager.dispute-statuses', compact('dispute_statuses'));
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
     * @param  \Illuminate\Http\DisputeStatus
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
            'dispute_status' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $dispute_status
         * @return \App\Models\DisputeStatus
         */

        $dispute_status = new DisputeStatus();

        $dispute_status->dispute_status = $request->dispute_status;

        /**
         * Save the type to the database
         */

        $dispute_status->save();

        /**
         *  Redirect user to dispute status page
         */
        if ($dispute_status) {

            // Log user activity
            activity()->log('Created dispute status');

            return redirect()->back()
                            ->with('status', 'Dispute status information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding dispute status information failed');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DisputeStatus
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DisputeStatus
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DisputeStatus
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
            'dispute_status' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $dispute_status
         * @return \App\Models\DisputeStatus
         */

        $dispute_status = DisputeStatus::findOrFail($id);

        $dispute_status->dispute_status = $request->dispute_status;

        /**
         * Update the type to the database
         */

        $dispute_status->update();

        /**
         *  Redirect user to dispute status page
         */
        if ($dispute_status) {

            // Log user activity
            activity()->log('Updated dispute status');

            return redirect()->back()
                            ->with('status', 'Dispute status information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating dispute status information failed');
        }
    }

    /**
     * Remove the specified status from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting status information from the database

        $status = DisputeStatus::findOrFail($id);

        $status->delete();

        if($status){

            // Log status activity
            activity()->log('Trashed dispute status account');

            return redirect()->back()->with('status','Dispute status information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing dispute status information failed, please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
    }
}
