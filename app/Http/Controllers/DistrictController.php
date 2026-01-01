<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\District;
use Illuminate\Http\Request;
use Facade\FlareClient\Http\Response;

class DistrictController extends Controller
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

        // Get all the regions and bind them to the create  view
        $regions = Region::get(['id','region']);

        // Get all the districts and bind them to the create  view
        $districts = District::with('region')->select(['id','district', 'region_id'])
                                ->orderBy('region_id', 'ASC')
                                ->orderBy('district', 'ASC')
                                ->paginate(10);

        return view('manager.districts', compact('regions', 'districts'));
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
            'regions' => ['required', 'string', 'max:255'],
            'districts' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array $district
         * @return \App\Models\District
         */

        $district = new District();

        $district->region_id = $request->regions;
        $district->district = $request->districts;

        /**
         * Save the type to the database
         */

        $district->save();

        /**
         *  Redirect user to District page
         */
        if ($district) {

            // Log user activity
            activity()->log('Created district');

            return redirect()->back()
                            ->with('status', 'District information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding district information failed');
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
            'district' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array $district
         * @return \App\Models\District
         */

        $district = District::findOrFail($id);

        $district->district = $request->district;

        /**
         * Update the type to the database
         */

        $district->update();

        /**
         *  Redirect user to district page
         */
        if ($district) {

            // Log user activity
            activity()->log('Updated district');

            return redirect()->back()
                            ->with('status', 'District information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating district information failed');
        }
    }

    /**
     * Remove the specified district from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting district information from the database

        $district = District::findOrFail($id);

        $district->delete();

        if($district){

            // Log district activity
            activity()->log('Trashed district');

            return redirect()->back()->with('status','District information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing district information failed, please try again.');
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
