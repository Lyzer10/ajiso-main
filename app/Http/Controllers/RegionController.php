<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
use Facade\FlareClient\Http\Response;

class RegionController extends Controller
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
        $regions = Region::select(['id', 'region'])->paginate(10);

        return view('manager.regions', compact('regions'));

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
            'region' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $region
         * @return \App\Models\Region
         */

        $region = new Region();

        $region->region = $request->region;

        /**
         * Save the type to the database
         */

        $region->save();

        /**
         *  Redirect user to Region page
         */
        if ($region) {

            // Log user activity
            activity()->log('Created region');

            return redirect()->back()
                            ->with('status', 'Region information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding region information failed');
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
     * Display the districts based on region.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDistricts($locale, $id)
    {
        $districts = Region::findOrFail($id)->districts;

        return $districts;
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
            'region' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $region
         * @return \App\Models\Region
         */

        $region = Region::findOrFail($id);

        $region->region = $request->region;

        /**
         * Update the type to the database
         */

        $region->update();

        /**
         *  Redirect user to region page
         */
        if ($region) {

            // Log user activity
            activity()->log('Updated region');

            return redirect()->back()
                            ->with('status', 'Region information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating region information failed');
        }
    }

    /**
     * Remove the specified region from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting region information from the database

        $region = Region::findOrFail($id);

        $region->delete();

        if($region){

            // Log user activity
            activity()->log('Trashed region');

            return redirect()->back()->with('status','Region information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing region information failed, please try again.');
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
