<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SurveyChoice;
use Facade\FlareClient\Http\Response;

class SurveyChoiceController extends Controller
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
        $survey_choices = SurveyChoice::get(['id', 'survey_choice', 'choice_abbr']);

        return view('manager.survey-choices', compact('survey_choices'));

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
            'survey_choice' => ['required', 'string', 'min:3', 'max:255'],
            'choice_abbr' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $survey_choice
         * @return \App\Models\SurveyChoice
         */

        $survey_choice = new SurveyChoice();

        $survey_choice->survey_choice = $request->survey_choice;
        $survey_choice->choice_abbr = $request->choice_abbr;

        /**
         * Save the type to the database
         */

        $survey_choice->save();

        /**
         *  Redirect user to SurveyChoice page
         */
        if ($survey_choice) {

            // Log user activity
            activity()->log('Created survey choice');

            return redirect()->back()
                            ->with('status', 'SurveyChoice information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding survey_choice information failed');
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
            'survey_choice' => ['required', 'string', 'min:3', 'max:255'],
            'choice_abbr' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $survey_choice
         * @return \App\Models\SurveyChoice
         */

        $survey_choice = SurveyChoice::findOrFail($id);

        $survey_choice->survey_choice = $request->survey_choice;
        $survey_choice->choice_abbr = $request->choice_abbr;

        /**
         * Update the type to the database
         */

        $survey_choice->update();

        /**
         *  Redirect user to survey choice  page
         */
        if ($survey_choice) {

            // Log user activity
            activity()->log('Updated survey choice');

            return redirect()->back()
                            ->with('status', 'Survey choice information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating survey choice  information failed');
        }
    }

    /**
     * Remove the specified survey_choice from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting survey_choice information from the database

        $survey_choice = SurveyChoice::findOrFail($id);

        $survey_choice->delete();

        if($survey_choice){

            // Log user activity
            activity()->log('Trashed survey choice');

            return redirect()->back()->with('status','Survey choice information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing survey choice information failed, please try again.');
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
