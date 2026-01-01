<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeOfService;
use Facade\FlareClient\Http\Response;

class TypeOfServiceController extends Controller
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
        $types_of_services = TypeOfService::get(['id', 'service_abbreviation', 'type_of_service']);

        return view('manager.types-of-services', compact('types_of_services'));
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
            'service_abbreviation' => ['required', 'string', 'min:3', 'max:255'],
            'service_name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $type_of_service
         * @return \App\Models\TypeOfService
         */

        $type_of_service = new TypeOfService();

        $type_of_service->service_abbreviation = $request->service_abbreviation;
        $type_of_service->type_of_service = $request->service_name;

        /**
         * Save the type to the database
         */

        $type_of_service->save();

        /**
         *  Redirect type_of_service to type of service page
         */
        if ($type_of_service) {

            // Log user activity
            activity()->log('Created type of service');

            return redirect()->back()
                            ->with('status', 'Type of service information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding type of service information failed');
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
            'service_abbreviation' => ['required', 'string', 'min:3', 'max:255'],
            'service_name' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $type_of_service
         * @return \App\Models\TypeOfService
         */

        $type_of_service = TypeOfService::findOrFail($id);

        $type_of_service->service_abbreviation = $request->service_abbreviation;
        $type_of_service->type_of_service = $request->service_name;

        /**
         * Update the type to the database
         */

        $type_of_service->update();

        /**
         *  Redirect type_of_service to type of services page
         */
        if ($type_of_service) {

            // Log user activity
            activity()->log('Updated type of service');

            return redirect()->back()
                            ->with('status', 'Type of service information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating type of service information failed');
        }
    }

    /**
     * Remove the specified type_of_service from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting type_of_service information from the database

        $type_of_service = TypeOfService::findOrFail($id);

        $type_of_service->delete();

        if($type_of_service){

            // Log user activity
            activity()->log('Trashed type of service');

            return redirect()->back()->with('status','Type of service information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing type of service information failed, please try again.');
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
