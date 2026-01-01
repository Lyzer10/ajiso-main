<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Metric;
use App\Models\MetricMeasure;
use Facade\FlareClient\Http\Response;

class MetricController extends Controller
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
        $metrics = Metric::with('metricMeasure')
                            ->get(['id', 'metric', 'metric_measure_id', 'metric_limit']);

        // Get all metric measures and pass them to the view
        $metric_measures = MetricMeasure::get(['id', 'metric_measure']);

        return view('manager.metrics', compact('metrics', 'metric_measures'));

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
            'metric' => ['required', 'string', 'min:3', 'max:255'],
            'metric_measure' => ['required', 'numeric', 'max:1000000000'],
            'metric_limit' => ['required', 'numeric', 'max:1000000000'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $metric
         * @return \App\Models\Metric
         */

        $metric = new Metric();

        $metric->metric = $request->metric;
        $metric->metric_measure_id = $request->metric_measure;
        $metric->metric_limit = $request->metric_limit;

        /**
         * Save the type to the database
         */

        $metric->save();

        /**
         *  Redirect user to metric page
         */
        if ($metric) {

            // Log user activity
            activity()->log('Created metric');

            return redirect()->back()
                            ->with('status', 'Metric information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding metric information failed');
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
            'metric' => ['required', 'string', 'min:3', 'max:255'],
            'metric_measure' => ['required', 'numeric', 'max:1000000000'],
            'metric_limit' => ['required', 'numeric', 'max:1000000000'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $metric
         * @return \App\Models\Metric
         */

        $metric = Metric::findOrFail($id);

        $metric->metric = $request->metric;
        $metric->metric_measure_id = $request->metric_measure;
        $metric->metric_limit = $request->metric_limit;

        /**
         * Update the type to the database
         */

        $metric->update();

        /**
         *  Redirect user to metric page
         */
        if ($metric) {

            // Log user activity
            activity()->log('Updated metric');

            return redirect()->back()
                            ->with('status', 'Metric information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating metric information failed');
        }
    }

    /**
     * Remove the specified metric from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting metric information from the database

        $metric = Metric::findOrFail($id);

        $metric->delete();

        if($metric){

            // Log user activity
            activity()->log('Trashed metric');

            return redirect()->back()->with('status','Metric information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing metric information failed, please try again.');
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
