<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MetricMeasure;
use Facade\FlareClient\Http\Response;

class MetricMeasureController extends Controller
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
        $metric_measures = MetricMeasure::get(['id', 'metric_measure']);

        return view('manager.metric-measures', compact('metric_measures'));

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
            'metric_measure' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $metric_measure
         * @return \App\Models\MetricMeasure
         */

        $metric_measure = new MetricMeasure();

        $metric_measure->metric_measure = $request->metric_measure;

        /**
         * Save the type to the database
         */

        $metric_measure->save();

        /**
         *  Redirect user to metric measure page
         */
        if ($metric_measure) {

            // Log user activity
            activity()->log('Created metric measure');

            return redirect()->back()
                            ->with('status', 'Metric measure information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding metric measure information failed');
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
            'metric_measure' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $metric_measure
         * @return \App\Models\MetricMeasure
         */

        $metric_measure = MetricMeasure::findOrFail($id);

        $metric_measure->metric_measure = $request->metric_measure;

        /**
         * Update the type to the database
         */

        $metric_measure->update();

        /**
         *  Redirect user to metric measure  page
         */
        if ($metric_measure) {

            // Log user activity
            activity()->log('Updated metric measure');

            return redirect()->back()
                            ->with('status', 'Metric measure information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating metric measure  information failed');
        }
    }

    /**
     * Remove the specified metric_measure from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting metric_measure information from the database

        $metric_measure = MetricMeasure::findOrFail($id);

        $metric_measure->delete();

        if($metric_measure){

            // Log user activity
            activity()->log('Trashed metric measure');

            return redirect()->back()->with('status','Metric Measure information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing metric measure information failed, please try again.');
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
