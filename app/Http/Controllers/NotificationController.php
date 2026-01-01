<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Notifications\CustomNotice;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
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
        $notifications = auth()->user()->unreadNotifications->take(100);

        return view('notifications.view', compact('notifications'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Eager load all active users
        $users = User::where('is_active', 1)
                    ->latest()
                    ->get(
                            [
                                'id','name','user_no','first_name',
                                'middle_name','last_name','email',
                                'tel_no', 'mobile_no'
                            ]
                    );
                        
        // Get all the beneficiaries
        $beneficiaries = Beneficiary::has('user')
                                    ->with('user')
                                    ->latest()
                                    ->get(['id','user_id']);

        // Get all the staff
        $staff = Staff::has('user')
                        ->with('user')
                        ->latest()
                        ->get(['id','user_id','office']);
                        
        return view('notifications.create', compact('users', 'staff', 'beneficiaries'));
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
         * Get a validator for an incoming search request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */

        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'publish_to' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:512'],
        ]);

        // Check if the request contains publish_to key
        if ($request->has('publish_to')) {

            // Assign the request keys to variables

            $title = $request->title;

            $publish_to = $request->publish_to;

            $priority = $request->priority;

            $message = $request->message;

            $res = '';

            $recipients = collect();

            // Check if the publish_to equals 'allUsers'
            if ($publish_to === 'allUsers') {

                // Load all the active user
                $users = User::where('is_active', 1)
                                ->latest()
                                ->get(
                                        [
                                            'id','name','user_no','first_name',
                                            'middle_name','last_name','email',
                                            'tel_no', 'mobile_no'
                                        ]
                                );

                // Prepare recipients
                $recipients = $users;

            }elseif ($publish_to === 'allLegalAidProviders') {

                // Get all the staff
                $staff = Staff::has('user')
                                ->with('user')
                                ->latest()
                                ->get(['id','user_id','office']);
                
                // Prepare the recipients
                $recipients = $staff;

            }elseif ($publish_to === 'allBeneficiaries') {

                // Get all the beneficiaries
                $beneficiaries = Beneficiary::has('user')
                                            ->with('user')
                                            ->latest()
                                            ->get(['id','user_id']);
                
                // Prepare the recipients
                $recipients = $beneficiaries;

            }elseif ($publish_to === 'targetlLegalAidProvider') {

                $this->validate($request, [
                    'legal_aid_provider' => ['required', 'numeric', 'max:255'],
                ]);

                $search = $request->legal_aid_provider;

                // Get all the target staff
                $staff = Staff::has('user')
                                ->with('user')
                                ->where('id', $search)
                                ->latest()
                                ->get(['id','user_id','office']);
                
                // Prepare the recipients                
                $recipients = $staff;

            }elseif ($publish_to === 'targetBeneficiary') {

                // validate individual publish_to value
                $this->validate($request, [
                    'beneficiary' => ['required', 'numeric', 'max:255'],
                ]);

                $search = $request->beneficiary;

                // Get all the beneficiaries
                $beneficiary = Beneficiary::has('user')
                                            ->with('user')
                                            ->where('id', $search)
                                            ->latest()
                                            ->get(['id','user_id']);
                // Prepare the recipients
                $recipients = $beneficiary;

            }else {
                //return $disputes with error;
                return redirect()->back()
                                ->withErrors('errors', 'Something went wrong, please try again.');
            }
            
            try {

                if(env('SEND_NOTIFICATIONS') == TRUE)
                {
                    // Send Email & Database notifications to recipients
                    Notification::send($recipients, new CustomNotice($title, $priority, $message));
                }
                
                // Log user activity
                activity()->log('Sent notification');

                //return with success status;
                return redirect()->route('notification.create', app()->getLocale())
                                ->with('status', 'Notification published, successfully.');

            } catch (\Throwable $th) {
                //throw $th;
                //return with error;
                return redirect()->back()
                                ->withErrors('errors', 'Could not publish notification, please try again.');
            }

        }else {
            //return with error;
            return redirect()->back()
                            ->withErrors('errors', 'Could not publish notification, please try again.');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markNotification(Request $request, $locale)
    {
        auth()->user()
            ->unreadNotifications
            ->when($request->input('id'), function ($query) use ($request){
                return $query->where('id', $request->input('id'));
            })
            ->markAsRead();
        
        return response()->noContent();
    }

    /**
     * Remove the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $locale)
    {
        // Delete all notifications from the database where created_at is less than last 30 days
        try {
            DB::table('notifications')->where('created_at','<=',Carbon::now()->subdays(30))->delete();

            return redirect()->route('notifications.list')
                                ->with('status', 'Deleted notifications, successfully.');

        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()
                                ->withErrors('errors', 'Could delete notifications, please try again.');
        }
    }
}
