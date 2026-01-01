<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Spatie\Activitylog\Models\Activity;

class MiscController extends Controller
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
     * Redirect to logs
     */
    public function logs()
    {
        // Get all the activity logs
        $activity_logs = Activity::with('causer:id,user_no,first_name,middle_name,last_name,user_role_id')
                                    ->latest()
                                    ->paginate(10);
        // Bind the logs to the view
        return view('misc.logs', compact('activity_logs'));
    }

    /**
     * Clean all logs from the database
     */
    public function cleanLogs()
    {
        //Clear and clean all the activity logs from the database
        try {
            Artisan::Call('activitylog:clean'); 
            
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->backup()
                        ->withErrors('errors', 'Could not clean logs, please try again.');
        }

        return redirect()->route('system.logs', app()->getLocale())
                        ->with('status', 'Logs cleared, successfully.');
    }

    /**
     * Redirect to trash
     */
    public function trash()
    {
        return view('misc.trash');
    }

    /**
     * Redirect to backup
     */
    public function backup()
    {
        return view('misc.backup');
    }

    /**
     * Backup current system state
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function backupNow(Request $request, $locale)
    {
        /**
         * Get a validator for an incoming request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */

        $this->validate($request, [
            'backup_type' => ['required', 'string', 'max:255'],
        ]);

        // Check if request has backup type
        if ($request->has('backup_type')) {

            $type = $request->backup_type;

            // TODO Finish this and deploy
            // Check if backup type is 'all' and backup all files
            if ($type === 'all') {
                
                // Backup only database
                try {
                    Artisan::Call('backup:run --only-db');
                
                    // Redirect back with success message
                    return redirect()->route('system.backup', app()->getLocale())
                                        ->with('status', 'Backing up database state, successfully.');
                } catch (\Throwable $th) {
                    //throw $th;
                    return redirect()->back()
                                ->withErrors('errors', 'Could not backup system files, please try again.');
                }

            }else {

                // Backup everything
                try {
                    Artisan::Call('backup:run --only-db');
                
                    // Redirect back with success message
                    return redirect()->route('system.backup', app()->getLocale())
                                        ->with('status', 'Backing up database state, successfully.');
                } catch (\Throwable $th) {
                    //throw $th;
                    return redirect()->back()
                                ->withErrors('errors', 'Could not backup system files, please try again.');
                }
                Artisan::Call('backup:run');

                // Redirect back with success message
                return redirect()->route('system.logs', app()->getLocale())
                ->with('status', 'Backing up system files, successfully.');
            }
            
        }else{

            return redirect()->back()
                                ->withErrors('errors', 'Something went wrong, please try again.');
        }

    }

    /**
     * Redirect to system manager
     */
    public function settings()
    {
        return view('misc.system-manager');
    }
}
