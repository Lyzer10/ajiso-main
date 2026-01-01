<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use Facade\FlareClient\Http\Response;

class IncomeController extends Controller
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
        $incomes = Income::get(['id', 'income']);

        return view('manager.income-groups', compact('incomes'));

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
            'income' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $income
         * @return \App\Models\Income
         */

        $income = new Income();

        $income->income = $request->income;

        /**
         * Save the type to the database
         */

        $income->save();

        /**
         *  Redirect user to Income page
         */
        if ($income) {

            // Log user activity
            activity()->log('Created income group');

            return redirect()->back()
                            ->with('status', 'Income information added');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Adding income information failed');
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
            'income' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array  $income
         * @return \App\Models\Income
         */

        $income = Income::findOrFail($id);

        $income->income = $request->income;

        /**
         * Update the type to the database
         */

        $income->update();

        /**
         *  Redirect user to income page
         */
        if ($income) {

            // Log user activity
            activity()->log('Updated income group');

            return redirect()->back()
                            ->with('status', 'Income information updated');
        }else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating income information failed');
        }
    }

    /**
     * Remove the specified income from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting income information from the database

        $income = Income::findOrFail($id);

        $income->delete();

        if($income){

            // Log user activity
            activity()->log('Trashed income group');

            return redirect()->back()->with('status','Income information trashed, successfully.');

        }else {
            return redirect()->back()->withErrors('errors','Trashing income group information failed, please try again.');
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
