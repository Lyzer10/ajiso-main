<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarriageForm;
use Facade\FlareClient\Http\Response;

class MarriageFormController extends Controller
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
        $marriage_forms = MarriageForm::get(['id', 'marriage_form']);

        return view('manager.marriage-forms', compact('marriage_forms'));

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
            'marriage_form' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $marriage_form
         * @return \App\Models\MarriageForm
         */

        $marriage_form = new MarriageForm();

        $marriage_form->marriage_form = $request->marriage_form;

        /**
         * Save the type to the database
         */

        $marriage_form->save();

        /**
         *  Redirect user to marriage form page
         */
        if ($marriage_form) {

            // Log user activity
            activity()->log('Created marriage form');

            return redirect()->back()
                            ->with('status', 'Marriage form information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding marriage form information failed');
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
            'marriage_form' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $marriage_form
         * @return \App\Models\MarriageForm
         */

        $marriage_form = MarriageForm::findOrFail($id);

        $marriage_form->marriage_form = $request->marriage_form;

        /**
         * Update the type to the database
         */

        $marriage_form->update();

        /**
         *  Redirect user to marriage form  page
         */
        if ($marriage_form) {

            // Log user activity
            activity()->log('Updated marriage form');

            return redirect()->back()
                            ->with('status', 'Marriage form information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating marriage form  information failed');
        }
    }

    /**
     * Remove the specified marriage_form from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting marriage_form information from the database

        $marriage_form = MarriageForm::findOrFail($id);

        $marriage_form->delete();

        if($marriage_form){

            // Log user activity
            activity()->log('Trashed marriage form');

            return redirect()->back()->with('status','Marriage form information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing marriage form information failed, please try again.');
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
