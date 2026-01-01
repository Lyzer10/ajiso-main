<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeOfCase;
use Facade\FlareClient\Http\Response;

class TypeOfCaseController extends Controller
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
        $types_of_cases = TypeOfCase::get(['id', 'type_of_case']);

        return view('manager.types-of-cases', compact('types_of_cases'));
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
            'case_name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $type_of_case
         * @return \App\Models\TypeOfCase
         */

        $type_of_case = new TypeOfCase();

        $type_of_case->type_of_case = $request->case_name;

        /**
         * Save the type to the database
         */

        $type_of_case->save();

        /**
         *  Redirect type_of_case to type of case page
         */
        if ($type_of_case) {

            // Log user activity
            activity()->log('Created type of case');

            return redirect()->back()
                            ->with('status', 'Type of case information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding type of case information failed');
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
            'case_name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $type_of_case
         * @return \App\Models\TypeOfCase
         */

        $type_of_case = TypeOfCase::findOrFail($id);

        $type_of_case->type_of_case = $request->case_name;

        /**
         * Update the type to the database
         */

        $type_of_case->update();

        /**
         *  Redirect type_of_case to type of cases page
         */
        if ($type_of_case) {

            // Log user activity
            activity()->log('Updated type of case');

            return redirect()->back()
                            ->with('status', 'Type of case information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating type of case information failed');
        }
    }

    /**
     * Remove the specified type_of_case from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting type_of_case information from the database

        $type_of_case = TypeOfCase::findOrFail($id);

        $type_of_case->delete();

        if($type_of_case){

            // Log user activity
            activity()->log('Trashed type of case');

            return redirect()->back()->with('status','Type of case information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing type of case information failed, please try again.');
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
