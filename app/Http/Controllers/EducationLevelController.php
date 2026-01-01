<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EducationLevel;
use Facade\FlareClient\Http\Response;

class EducationLevelController extends Controller
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
        $education_levels = EducationLevel::get(['id', 'education_level']);

        return view('manager.education-levels', compact('education_levels'));

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
            'education_level' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $education_level
         * @return \App\Models\EducationLevel
         */

        $education_level = new EducationLevel();

        $education_level->education_level = $request->education_level;

        /**
         * Save the type to the database
         */

        $education_level->save();

        /**
         *  Redirect user to marriage form page
         */
        if ($education_level) {

            // Log user activity
            activity()->log('Created education level');

            return redirect()->back()
                            ->with('status', 'Education level information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding education level information failed');
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
            'education_level' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $education_level
         * @return \App\Models\EducationLevel
         */

        $education_level = EducationLevel::findOrFail($id);

        $education_level->education_level = $request->education_level;

        /**
         * Update the type to the database
         */

        $education_level->update();

        /**
         *  Redirect user to marriage form  page
         */
        if ($education_level) {

            // Log user activity
            activity()->log('Updated education level');

            return redirect()->back()
                            ->with('status', 'Education level information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating education level information failed');
        }
    }

    /**
     * Remove the specified education_level from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting education_level information from the database

        $education_level = EducationLevel::findOrFail($id);

        $education_level->delete();

        if($education_level){

            // Log user activity
            activity()->log('Trashed education level');

            return redirect()->back()->with('status','Education level information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing education_level information failed, please try again.');
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
