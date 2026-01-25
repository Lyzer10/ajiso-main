<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Dispute;
use Illuminate\Support\Str;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Models\AssignmentRequest;
use App\Models\Staff;
use Illuminate\Support\Facades\Gate;
use App\Notifications\AssignmentRequest as AssignmentRequestNotice;
use App\Notifications\RequestAccepted;
use App\Notifications\RequestRejected;
use Illuminate\Support\Facades\Notification;

class AssignmentRequestController extends Controller
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

        // Get all the requests nd bind them to the  view
        $assignment_requests = AssignmentRequest::has('requestedBy')
            ->with(
                'requestedBy:first_name,middle_name,last_name,user_no',
                'dispute:id,dispute_no'
            )
            ->select(
                [
                    'id',
                    'dispute_id',
                    'reason_description',
                    'staff_id',
                    'request_status',
                    'created_at'
                ]
            )
            ->latest()
            ->paginate(10);

        return view('dispute-assignment.list', compact('assignment_requests'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function myList()
    {
        // Check if current user is an authenticated staff
        if (Gate::denies(['isBeneficiary'])) {
            // Get the authenticated staff
            $staff = User::has('staff')
                ->with('staff')
                ->findOrFail(
                    auth()->user()->id
                )->staff->id ?? NULL;

            // Get disputes requests sent by the selected staff
            $assignment_requests = AssignmentRequest::has('requestedBy')
                ->with(
                    'requestedBy:first_name,middle_name,last_name,user_no',
                    'dispute:id,dispute_no'
                )
                ->where('staff_id',  $staff)
                ->select(
                    [
                        'id',
                        'dispute_id',
                        'reason_description',
                        'staff_id',
                        'request_status',
                        'created_at'
                    ]
                )
                ->paginate(10);
        } else {
            return redirect()->back()
                ->withErrors('errors', 'You are not authorized to perform this action!');
        }

        //return $disputes;
        return view('dispute-assignment.view', compact('assignment_requests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale, $id)
    {
        $role = optional(auth()->user()->role)->role_abbreviation;
        $isAdminUser = in_array($role, ['admin', 'superadmin'], true);

        if ($id === 'all' && !is_numeric($id)) {

            if ($isAdminUser) {
                $disputes = Dispute::has('reportedBy')
                    ->with('reportedBy')
                    ->latest()
                    ->get([
                        'id',
                        'dispute_no',
                        'beneficiary_id',
                        'dispute_status_id',
                        'reported_on'
                    ]);
            } else {
                // Get the authenticated staff
                $staff = User::has('staff')
                    ->with('staff')
                    ->findOrFail(
                        auth()->user()->id
                    )
                    ->staff->id ?? NULL;

                $disputes = Dispute::has('reportedBy')
                    ->with('reportedBy')
                    ->where('staff_id',  $staff)
                    ->latest()
                    ->get([
                        'id',
                        'dispute_no',
                        'beneficiary_id',
                        'dispute_status_id',
                        'reported_on'
                    ]);
            }

            // create an empty collection to help render view logic
            $dispute = collect();

            // return view compacted with dispute(s) and staff info
            return view('dispute-assignment.create', compact('disputes', 'dispute'));
        } else {

            if ($isAdminUser) {
                // Find dispute information by Id (no staff restriction)
                $dispute = Dispute::has('reportedBy')
                    ->with('reportedBy')
                    ->findOrFail($id);
            } else {
                // Get the authenticated staff
                $staff = User::has('staff')
                    ->with('staff')
                    ->findOrFail(
                        auth()->user()->id
                    )
                    ->staff->id ?? NULL;

                // Find dispute information by Id and return a edit view
                $dispute = Dispute::has('reportedBy')
                    ->with('reportedBy')
                    ->where('staff_id',  $staff)
                    ->findOrFail($id);
            }

            // create an empty collection to help render view logic
            $disputes = collect();

            // return view compacted with dispute(s) and staff info
            return view('dispute-assignment.create', compact('dispute', 'disputes'));
        }
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
            'dispute' => ['required', 'string', 'max:255'],
            'reason_description' => ['required', 'string', 'max:255'],
        ]);

        $role = optional(auth()->user()->role)->role_abbreviation;
        $isAdminUser = in_array($role, ['admin', 'superadmin'], true);

        // Get the dispute
        $dispute = Dispute::findOrFail((int) $request->dispute) ?? NULL;

        // Get the authenticated staff (requester)
        $staff = User::has('staff')
            ->with('staff')
            ->findOrFail(
                auth()->user()->id
            )
            ->staff->id ?? NULL;

        if ($isAdminUser) {
            $staff = $dispute->staff_id ?? null;
            if (is_null($staff)) {
                return redirect()->back()
                    ->withErrors('errors', 'This dispute has no assigned legal aid provider to request reassignment.');
            }
        }

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array $assignment_request
         * @return \App\Models\AssignmentRequest
         */

        $assignment_request = new AssignmentRequest();

        $assignment_request->staff_id = $staff;
        $assignment_request->dispute_id = $request->dispute;
        $assignment_request->reason_description = $request->reason_description;

        /**
         * Save the type to the database
         */

        $assignment_request->save();

        /**
         *  Redirect user to District page
         */
        if ($assignment_request) {

            // Log user activity
            activity()->log('Sent re(un)assignment request');


            $staff = Staff::with('user.designation')
                ->findOrFail($assignment_request->staff_id);

            $staff_title = trim((string) optional($staff->user->designation)->name);
            $staff_name  = trim(implode(' ', array_filter([
                $staff->user->first_name ?? '',
                $staff->user->middle_name ?? '',
                $staff->user->last_name ?? '',
            ])));
            $staff_display_name = $staff_name;
            if ($staff_title !== '' && strtolower($staff_title) !== 'other') {
                $staff_display_name = trim($staff_title . ' ' . $staff_name);
            }

            $reason = trim((string) $assignment_request->reason_description);
            $reason_snippet = Str::limit($reason, 160, '...');

            //Send SMS, Email & Database notifications to legal aid provider

            /**
             * Send sms, email & database notification
             */

            try {
                // SMS

                if (env('SEND_NOTIFICATIONS') == TRUE) {
                    # Notify requester via SMS
                    $staff_dest_addr = SmsService::normalizeRecipient($staff->user->tel_no);
                    $staff_recipients = ['recipient_id' => 1, 'dest_addr' => $staff_dest_addr];
                    $sms = new SmsService();
                    $staff_message = 'Habari, ' . $staff_display_name .
                        ', AJISO inapenda kukutaarifu kuwa, ombi lako la kubadilishiwa shauri' .
                        ' lenye namba ya usajili No. ' . $dispute->dispute_no . ' limepokelewa' .
                        '. Tembelea Mfumo wa ALAS kujua zaidi.' .
                        ' Ahsante.';
                    $sms->sendSMS($staff_recipients, $staff_message);

                    // Database & email 

                    # Notify requester via email & DB
                    Notification::send($staff->user, new AssignmentRequestNotice($staff, $dispute, $staff_message));

                    $adminUsers = User::where('is_active', 1)
                        ->whereHas('role', function ($query) {
                            $query->whereIn('role_abbreviation', ['admin', 'superadmin']);
                        })
                        ->get(['id', 'first_name', 'middle_name', 'last_name', 'email', 'tel_no', 'salutation_id']);

                    $admin_message = 'Habari, ombi la kubadilishiwa shauri lenye namba ya usajili No. ' .
                        $dispute->dispute_no . ' limetumwa na ' . $staff_display_name .
                        '. Sababu: ' . ($reason_snippet ?: 'N/A') .
                        '. Tafadhali ingia mfumo wa ALAS kuthibitisha ombi hili. Ahsante.';

                    foreach ($adminUsers as $admin) {
                        $admin_dest_addr = SmsService::normalizeRecipient($admin->tel_no);
                        $admin_recipients = ['recipient_id' => 1, 'dest_addr' => $admin_dest_addr];
                        $sms->sendSMS($admin_recipients, $admin_message);
                    }

                    if ($adminUsers->isNotEmpty()) {
                        Notification::send($adminUsers, new AssignmentRequestNotice($staff, $dispute, $admin_message));
                    }
                }
            } catch (\Throwable $th) {
                throw $th;
            }

            $redirectRoute = 'disputes.request.list';
            $routeParams = ['locale' => app()->getLocale()];

            if (in_array($role, ['clerk', 'paralegal', 'staff'], true)) {
                $staffId = optional(auth()->user()->staff)->id;
                if ($staffId) {
                    $redirectRoute = 'disputes.request.my-list';
                    $routeParams = ['locale' => app()->getLocale(), 'staff' => $staffId];
                }
            }

            return redirect()->to(route($redirectRoute, $routeParams, false))
                ->with('status', 'Reassignment request sent, successfully.');
        } else {
            return redirect()->back()
                ->withErrors('errors', 'Sending reassignment request failed, please try again.');
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
    public function acceptRequest(Request $request, $locale, $id)
    {
        $role = optional(auth()->user()->role)->role_abbreviation;
        if (!in_array($role, ['admin', 'superadmin'], true)) {
            return redirect()->back()
                ->withErrors('errors', 'You are not authorized to perform this action.');
        }

        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'res' => ['required', 'string', 'min:3', 'max:255']
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array $assignment_request
         * @return \App\Models\AssignmentRequest
         */

        $assignment_request = AssignmentRequest::findOrFail($id);

        if ($assignment_request->request_status !== 'pending') {
            return redirect()->back()
                ->withErrors('errors', 'This request has already been processed.');
        }

        $assignment_request->request_status = $request->res;

        $assignment_request->update();

        /**
         *  Redirect user to district page
         */
        if ($assignment_request) {

            // Log user activity
            activity()->log('Accepted re(un)assignment request');

            // Get the authenticated staff
            $staff = Staff::with('user.designation')
                ->findOrFail((int) $assignment_request->staff_id);

            // Get the dispute
            $dispute = Dispute::findOrFail((int) $assignment_request->dispute_id) ?? NULL;

            // Staff infos
            $staff_title = trim((string) optional($staff->user->designation)->name);
            $staff_name = trim(implode(' ', array_filter([
                $staff->user->first_name ?? '',
                $staff->user->middle_name ?? '',
                $staff->user->last_name ?? '',
            ])));
            $staff_display_name = $staff_name;
            if ($staff_title !== '' && strtolower($staff_title) !== 'other') {
                $staff_display_name = trim($staff_title . ' ' . $staff_name);
            }

            $staff_dest_addr = SmsService::normalizeRecipient($staff->user->tel_no);
            $staff_recipients = ['recipient_id' => 1, 'dest_addr' => $staff_dest_addr];

            $staff_message = 'Habari, ' . $staff_display_name .
                ', AJISO inapenda kukutaarifu kuwa, ombi lako la kubadilishiwa shauri' .
                ' lenye namba ya usajili No. ' . $dispute->dispute_no . ' limekubaliwa' .
                '. Tembelea Mfumo wa ALAS kujua zaidi.' .
                ' Ahsante.';

            //Send SMS, Email & Database notifications to legal aid provider

            /**
             * Send sms, email & database notification
             */

            try {
                // SMS
                if (env('SEND_NOTIFICATIONS') == TRUE) {

                    # Notify new assigned assigned via SMS
                    $sms = new SmsService();
                    $sms->sendSMS($staff_recipients, $staff_message);

                    // Database & email 

                    # Notify new assigned assigned via email & DB
                    Notification::send($staff->user, new RequestAccepted($staff, $dispute, $staff_message));
                }
            } catch (\Throwable $th) {
                throw $th;
            }

            return redirect()->to(route('dispute.assign', [
                'locale' => app()->getLocale(),
                'dispute' => $assignment_request->dispute_id,
            ], false))
                ->with('status', 'You accepted a reassignment request, please assign a new assignment.');
        } else {
            return redirect()->back()
                ->withErrors('errors', 'Accepting a reassignment request failed, please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rejectRequest(Request $request, $locale, $id)
    {
        $role = optional(auth()->user()->role)->role_abbreviation;
        if (!in_array($role, ['admin', 'superadmin'], true)) {
            return redirect()->back()
                ->withErrors('errors', 'You are not authorized to perform this action.');
        }
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'res' => ['required', 'string', 'min:3', 'max:255']
        ]);

        /**
         * Create a new user instance for a valid registration.
         *
         * @param  array $assignment_request
         * @return \App\Models\AssignmentRequest
         */

        $assignment_request = AssignmentRequest::findOrFail($id);

        if ($assignment_request->request_status !== 'pending') {
            return redirect()->back()
                ->withErrors('errors', 'This request has already been processed.');
        }

        $assignment_request->request_status = $request->res;

        $assignment_request->update();

        /**
         *  Redirect user to district page
         */
        if ($assignment_request) {

            // Log user activity
            activity()->log('Rejected re(un)assignment request');


            // Get the authenticated staff
            $staff = Staff::with('user.designation')
                ->findOrFail((int) $assignment_request->staff_id);

            // Get the dispute
            $dispute = Dispute::findOrFail((int) $assignment_request->dispute_id) ?? NULL;

            // Staff infos
            $staff_title = trim((string) optional($staff->user->designation)->name);
            $staff_name = trim(implode(' ', array_filter([
                $staff->user->first_name ?? '',
                $staff->user->middle_name ?? '',
                $staff->user->last_name ?? '',
            ])));
            $staff_display_name = $staff_name;
            if ($staff_title !== '' && strtolower($staff_title) !== 'other') {
                $staff_display_name = trim($staff_title . ' ' . $staff_name);
            }

            $staff_dest_addr = SmsService::normalizeRecipient($staff->user->tel_no);
            $staff_recipients = ['recipient_id' => 1, 'dest_addr' => $staff_dest_addr];

            $staff_message = 'Habari, ' . $staff_display_name .
                ', AJISO inapenda kukutaarifu kuwa, ombi lako la kubadilishiwa shauri' .
                ' lenye namba ya usajili No. ' . $dispute->dispute_no . ' limekataliwa' .
                '. Tembelea Mfumo wa ALAS kujua zaidi.' .
                ' Ahsante.';

            //Send SMS, Email & Database notifications to legal aid provider

            /**
             * Send sms, email & database notification
             */

            try {
                // SMS
                if (env('SEND_NOTIFICATIONS') == TRUE) {

                    # Notify new assigned assigned via SMS
                    $sms = new SmsService();
                    $sms->sendSMS($staff_recipients, $staff_message);

                    // Database & email 

                    # Notify new assigned assigned via email & DB
                    Notification::send($staff->user, new RequestRejected($staff, $dispute, $staff_message));
                }
            } catch (\Throwable $th) {
                throw $th;
            }

            return redirect()->back()
                ->with('status', 'You rejected a reassignment request, successfully.');
        } else {
            return redirect()->back()
                ->withErrors('errors', 'Rejecting a reassignment request failed, please try again.');
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
