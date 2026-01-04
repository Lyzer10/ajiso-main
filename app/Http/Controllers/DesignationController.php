<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
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
        $designations = Designation::get(['id', 'name']);

        return view('manager.designations', compact('designations'));

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
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $designation
         * @return \App\Models\Designation
         */

        $designation = new Designation();

        $designation->name = $request->name;

        /**
         * Save the type to the database
         */

        $designation->save();

        /**
         *  Redirect user to designation page
         */
        if ($designation) {
            return redirect()->back()
                            ->with('status', 'Designation information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding designation information failed');
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
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $designation
         * @return \App\Models\Designation
         */

        $designation = Designation::findOrFail($id);

        $designation->name = $request->name;

        /**
         * Update the type to the database
         */

        $designation->update();

        /**
         *  Redirect user to dispute status page
         */
        if ($designation) {
            return redirect()->back()
                            ->with('status', 'Dispute status information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating dispute status information failed');
        }
    }

    /**
     * Remove the specified designation from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting designation information from the database

        $designation = Designation::findOrFail($id);

        $designation->delete();

        if($designation){

            // Log designation activity
            activity()->log('Trashed designation account');

            return redirect()->back()->with('status','Designation information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing designation information failed, please try again.');
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
