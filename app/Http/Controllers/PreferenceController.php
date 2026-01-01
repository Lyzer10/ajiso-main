<?php

namespace App\Http\Controllers;

use App\Models\Preference;
use Illuminate\Http\Request;

class PreferenceController extends Controller
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
        $preferences = Preference::firstOrFail();

        return view('misc.preferences', compact('preferences'));
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
        //
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
     * Update the preference resource in storage.
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
            'site_abbr' => ['required', 'max:10'],
            'site_name' => ['required', 'max:100'],
            'org_abbr' => ['required', 'max:10'],
            'org_name' => ['required', 'max:100'],
            'org_site' => ['required', 'url', 'max:50'],
            'org_email' => ['required', 'email', 'max:50'],
            'org_tel' => ['required','string', 'max:50'],
            'language' => ['required', 'string', 'max:50'],
            'currency' => ['required','max:255'],
            'date_format' => ['required', 'string'],
            'notification' => ['required', 'string'],
        ]);

        /**
         * Create a new preference instance for a valid registration.
         *
         * @param  array  $preference
         * @return \App\Models\Preference
         */

        $preference = Preference::firstOrFail();

        $preference->sys_abbr = $request->site_abbr;
        $preference->sys_name = $request->site_name;
        $preference->org_abbr = $request->org_abbr;
        $preference->org_name = $request->org_name;
        $preference->org_site = $request->org_site;
        $preference->org_email = $request->org_email;
        $preference->org_tel = $request->org_tel;
        $preference->language = $request->language;
        $preference->currency_format = $request->currency;
        $preference->date_format = $request->date_format;
        $preference->notification_mode = $request->notification;

        /**
         * Save the preference to the database
         */

        $preference->update();

        /**
         *  Redirect user to preferences list
         */

        if ($preference) {
            return redirect()->back()
                            ->with('status', 'Preferences information updated');

        } else {
            return redirect()->back()
                            ->withErrors('errors', 'Updating preferences information failed, please try again');
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
