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
use Illuminate\Validation\Rule;

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
//hello
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = (int) request('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min($perPage, 100);

        // Get all the requests nd bind them to the  view
        $assignment_requests = AssignmentRequest::query()
            ->with(
                'requestedBy:first_name,middle_name,last_name,user_no',
                'requesterUser:id,first_name,middle_name,last_name,user_no',
                'dispute:id,dispute_no',
                'targetStaff.user:id,first_name,middle_name,last_name,salutation_id',
                'targetStaff.center:id,name'
            )
            ->select(
                [
                    'id',
                    'dispute_id',
                    'reason_description',
                    'staff_id',
                    'requester_user_id',
                    'target_staff_id',
                    'request_status',
                    'created_at'
                ]
            )
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        $availableStaff = Staff::has('user')
            ->with('user.designation:id,name', 'center:id,name')
            ->where('type', 'staff')
            ->whereHas('user', function ($query) {
                $query->where('is_active', 1);
            })
            ->whereHas('user.role', function ($query) {
                $query->where('role_abbreviation', 'staff');
            })
            ->get(['id', 'user_id', 'center_id']);

        return view('dispute-assignment.list', compact('assignment_requests', 'availableStaff'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function myList()
    {
        // Check if current user is an authenticated staff
        if (Gate::denies('isBeneficiary')) {
            // Get the authenticated staff
            $staff = User::has('staff')
                ->with('staff')
                ->findOrFail(
                    auth()->user()->id
                )->staff->id ?? NULL;

            // Get disputes requests sent by the selected staff
            $assignment_requests = AssignmentRequest::query()
                ->with(
                    'requestedBy:first_name,middle_name,last_name,user_no',
                    'requesterUser:id,first_name,middle_name,last_name,user_no',
                    'dispute:id,dispute_no',
                    'targetStaff.user:id,first_name,middle_name,last_name,salutation_id',
                    'targetStaff.center:id,name'
                )
                ->where('staff_id',  $staff)
                ->select(
                    [
                        'id',
                        'dispute_id',
                        'reason_description',
                        'staff_id',
                        'requester_user_id',
                        'target_staff_id',
                        'request_status',
                        'created_at'
                    ]
                )
                ->latest()
                ->paginate(10, ['*'], 'requests_page');

            // Get disputes currently assigned to the selected staff
            $assigned_disputes = Dispute::has('reportedBy')
                ->with(
                    'reportedBy:first_name,middle_name,last_name,user_no',
                    'disputeStatus:id,dispute_status'
                )
                ->where('staff_id', $staff)
                ->select([
                    'id',
                    'dispute_no',
                    'beneficiary_id',
                    'reported_on',
                    'dispute_status_id'
                ])
                ->latest()
                ->paginate(10, ['*'], 'assigned_page');
        } else {
            return redirect()->back()
                ->withErrors('errors', 'You are not authorized to perform this action!');
        }

        //return $disputes;
        $showAssignedCases = true;
        return view('dispute-assignment.view', compact('assignment_requests', 'assigned_disputes', 'showAssignedCases'));
    }

    /**
     * Display a listing of the resource for paralegal requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function myParalegalList()
    {
        $role = optional(auth()->user()->role)->role_abbreviation;
        if ($role !== 'paralegal') {
            return redirect()->back()
                ->withErrors('errors', 'You are not authorized to perform this action.');
        }

        $userId = auth()->id();

        $assignment_requests = AssignmentRequest::query()
            ->with(
                'requesterUser:id,first_name,middle_name,last_name,user_no',
                'dispute:id,dispute_no',
                'targetStaff.user:id,first_name,middle_name,last_name,salutation_id',
                'targetStaff.center:id,name'
            )
            ->where('requester_user_id', $userId)
            ->select(
                [
                    'id',
                    'dispute_id',
                    'reason_description',
                    'staff_id',
                    'requester_user_id',
                    'target_staff_id',
                    'request_status',
                    'created_at'
                ]
            )
            ->latest()
            ->paginate(10, ['*'], 'requests_page');

        $assigned_disputes = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $showAssignedCases = false;

        return view('dispute-assignment.view', compact('assignment_requests', 'assigned_disputes', 'showAssignedCases'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($locale, $id)
    {
        if (is_numeric($id)) {
            return redirect()->route('dispute.show', [app()->getLocale(), $id])
                ->with('status', 'Use the reassignment button on the dispute profile.');
        }

        return redirect()->route('disputes.list', app()->getLocale())
            ->with('status', 'Use the reassignment button on the dispute profile.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $role = optional($user->role)->role_abbreviation;
        $isAdminUser = in_array($role, ['admin', 'superadmin'], true);
        $isParalegalUser = $role === 'paralegal';
        $isStaffUser = $role === 'staff';
        $requiresTargetStaff = $isAdminUser; // Admin needs to select target staff
        $reasonIsRequired = !$isAdminUser; // Reason required only for non-admins

        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $staffExistsRule = Rule::exists('staff', 'id')->where(function ($query) {
            $query->where('type', 'staff');
        });

        $this->validate($request, [
            'dispute' => ['required', 'integer', 'exists:disputes,id'],
            'reason_description' => [
                Rule::requiredIf($reasonIsRequired),
                'nullable',
                'string', 
                'max:255'
            ],
            'target_staff_id' => [
                Rule::requiredIf($requiresTargetStaff),
                'nullable',
                'integer',
                $staffExistsRule,
            ],
        ]);
        $isAdminUser = in_array($role, ['admin', 'superadmin'], true);

        // Get the dispute
        $dispute = Dispute::findOrFail((int) $request->dispute) ?? NULL;

        // Get the authenticated staff (requester) if available
        $staff = null;
        if ($isStaffUser) {
            $staff = User::has('staff')
                ->with('staff')
                ->findOrFail($user->id)
                ->staff->id ?? null;
        }

        if ($isAdminUser) {
            // For admin, staff is the current dispute's staff (who is requesting reassignment)
            $staff = $dispute->staff_id ?? null;
            if (is_null($staff)) {
                return redirect()->back()
                    ->withErrors('errors', 'This dispute has no assigned legal aid provider to request reassignment.');
            }
        }
        if ($isStaffUser && !$isAdminUser && !empty($dispute->staff_id) && (int) $dispute->staff_id !== (int) $staff) {
            return redirect()->back()
                ->withErrors('errors', 'You are not assigned to this dispute.');
        }

        $targetStaffId = $request->input('target_staff_id');
        if (!is_null($targetStaffId) && !is_null($staff) && (int) $targetStaffId === (int) $staff) {
            return redirect()->back()
                ->withErrors('errors', 'Please select a different legal aid provider.');
        }

        /**
         * If admin is reassigning directly, update the dispute and skip request creation
         */
        if ($isAdminUser) {
            $previousStaffId = $dispute->staff_id;

            $reasonDescription = trim((string) $request->input('reason_description', ''));
            if ($reasonDescription === '') {
                $reasonDescription = 'Admin direct reassignment';
            }

            // Record the reassignment request for audit/history purposes
            AssignmentRequest::create([
                'staff_id' => $previousStaffId,
                'dispute_id' => $dispute->id,
                'reason_description' => $reasonDescription,
                'target_staff_id' => $targetStaffId,
                'requester_user_id' => $user ? $user->id : null,
                'request_status' => 'accepted',
            ]);

            // Get the new staff member
            $newStaff = Staff::with('user.designation')
                ->findOrFail($targetStaffId);

            // Admin reassigns directly
            $dispute->staff_id = $targetStaffId;
            $dispute->save();

            // Log activity
            activity()->log('Directly reassigned case');

            // Build notification message
            $staff_title = trim((string) optional($newStaff->user->designation)->name);
            $staff_name = trim(implode(' ', array_filter([
                $newStaff->user->first_name ?? '',
                $newStaff->user->middle_name ?? '',
                $newStaff->user->last_name ?? '',
            ])));
            $staff_display_name = $staff_name;
            if ($staff_title !== '' && strtolower($staff_title) !== 'other') {
                $staff_display_name = trim($staff_title . ' ' . $staff_name);
            }

            $staff_message = 'Habari, ' . $staff_display_name .
                ', AJISO inapenda kukutaarifu kuwa, shauri lenye namba ya usajili No. ' . $dispute->dispute_no . 
                ' limekupewa. Tembelea Mfumo wa ALAS kujua zaidi.' .
                ' Ahsante.';

            // Send notifications
            try {
                if (env('SEND_NOTIFICATIONS') == TRUE) {
                    $staff_dest_addr = SmsService::normalizeRecipient($newStaff->user->tel_no);
                    $staff_recipients = ['recipient_id' => 1, 'dest_addr' => $staff_dest_addr];
                    $sms = new SmsService();
                    $sms->sendSMS($staff_recipients, $staff_message);

                    Notification::send($newStaff->user, new \App\Notifications\StaffDisputeAssigned($newStaff, $dispute, $staff_message));
                }
            } catch (\Throwable $th) {
                // Log error but don't fail the reassignment
            }

            return redirect()->to(route('dispute.show', [app()->getLocale(), $dispute->id], false))
                ->with('status', 'Case reassigned successfully and notification sent to the assigned staff member.');
        }

        /**
         * Create a new assignment request for non-admin users.
         *
         * @param  array $assignment_request
         * @return \App\Models\AssignmentRequest
         */

        $assignment_request = new AssignmentRequest();

        $assignment_request->staff_id = $staff;
        $assignment_request->dispute_id = $request->dispute;
        $assignment_request->reason_description = $request->reason_description;
        $assignment_request->target_staff_id = $targetStaffId;
        $assignment_request->requester_user_id = $user ? $user->id : null;

        /**
         * Save the request to the database
         */

        $assignment_request->save();

        /**
         *  Redirect user to District page
         */
        if ($assignment_request) {

            // Log user activity
            activity()->log('Sent re(un)assignment request');


            $requesterUser = $user;
            $staff_display_name = '';
            if ($assignment_request->staff_id) {
                $staff = Staff::with('user.designation')
                    ->findOrFail($assignment_request->staff_id);
                $requesterUser = $staff->user;
                $staff_title = trim((string) optional($staff->user->designation)->name);
                $staff_name  = trim(implode(' ', array_filter([
                    $staff->user->first_name ?? '',
                    $staff->user->middle_name ?? '',
                    $staff->user->last_name ?? '',
                ])));
                $staff_display_name = $staff_title !== '' && strtolower($staff_title) !== 'other'
                    ? trim($staff_title . ' ' . $staff_name)
                    : $staff_name;
            } elseif ($requesterUser) {
                $staff_display_name = trim(implode(' ', array_filter([
                    $requesterUser->first_name ?? '',
                    $requesterUser->middle_name ?? '',
                    $requesterUser->last_name ?? '',
                ])));
            }

            $reason = trim((string) $assignment_request->reason_description);
            $reason_snippet = Str::limit($reason, 160, '...');
            $targetStaff = null;
            $targetStaffLabel = null;
            if ($assignment_request->target_staff_id) {
                $targetStaff = Staff::with('user.designation', 'center')
                    ->find($assignment_request->target_staff_id);
                if ($targetStaff && $targetStaff->user) {
                    $targetTitle = trim((string) optional($targetStaff->user->designation)->name);
                    $targetName = trim(implode(' ', array_filter([
                        $targetStaff->user->first_name ?? '',
                        $targetStaff->user->middle_name ?? '',
                        $targetStaff->user->last_name ?? '',
                    ])));
                    $targetStaffLabel = $targetTitle !== '' && strtolower($targetTitle) !== 'other'
                        ? trim($targetTitle . ' ' . $targetName)
                        : $targetName;
                }
            }

            //Send SMS, Email & Database notifications to legal aid provider

            /**
             * Send sms, email & database notification
             */

            try {
                // SMS

                if (env('SEND_NOTIFICATIONS') == TRUE) {
                    # Notify requester via SMS
                    $staff_dest_addr = $requesterUser ? SmsService::normalizeRecipient($requesterUser->tel_no) : null;
                    $staff_recipients = $staff_dest_addr ? ['recipient_id' => 1, 'dest_addr' => $staff_dest_addr] : null;
                    $sms = new SmsService();
                    $staff_message = 'Habari, ' . $staff_display_name .
                        ', AJISO inapenda kukutaarifu kuwa, ombi lako la kubadilishiwa shauri' .
                        ' lenye namba ya usajili No. ' . $dispute->dispute_no . ' limepokelewa' .
                        '. Tembelea Mfumo wa ALAS kujua zaidi.' .
                        ' Ahsante.';
                    if ($staff_recipients) {
                        $sms->sendSMS($staff_recipients, $staff_message);
                    }

                    // Database & email 

                    # Notify requester via email & DB
                    if ($requesterUser) {
                        Notification::send($requesterUser, new AssignmentRequestNotice($requesterUser, $dispute, $staff_message));
                    }

                    $adminUsers = User::where('is_active', 1)
                        ->whereHas('role', function ($query) {
                            $query->whereIn('role_abbreviation', ['admin', 'superadmin']);
                        })
                        ->get(['id', 'first_name', 'middle_name', 'last_name', 'email', 'tel_no', 'salutation_id']);

                    $targetLine = $targetStaffLabel ? ' Msaidizi anayependekezwa: ' . $targetStaffLabel . '.' : '';
                    $admin_message = 'Habari, ombi la kubadilishiwa shauri lenye namba ya usajili No. ' .
                        $dispute->dispute_no . ' limetumwa na ' . $staff_display_name .
                        '. Sababu: ' . ($reason_snippet ?: 'N/A') . '.' . $targetLine .
                        '. Tafadhali ingia mfumo wa ALAS kuthibitisha ombi hili. Ahsante.';

                    foreach ($adminUsers as $admin) {
                        $admin_dest_addr = SmsService::normalizeRecipient($admin->tel_no);
                        $admin_recipients = ['recipient_id' => 1, 'dest_addr' => $admin_dest_addr];
                        $sms->sendSMS($admin_recipients, $admin_message);
                    }

                    if ($adminUsers->isNotEmpty()) {
                        if ($requesterUser) {
                            Notification::send($adminUsers, new AssignmentRequestNotice($requesterUser, $dispute, $admin_message));
                        }
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
        $staffExistsRule = Rule::exists('staff', 'id')->where(function ($query) {
            $query->where('type', 'staff');
        });

        $this->validate($request, [
            'res' => ['required', 'string', 'min:3', 'max:255'],
            'target_staff_id' => ['nullable', 'integer', $staffExistsRule],
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

        $targetStaffId = $request->input('target_staff_id') ?: $assignment_request->target_staff_id;
        if (is_null($targetStaffId)) {
            return redirect()->back()
                ->withErrors('errors', 'Please select a legal aid provider to assign.');
        }

        $assignment_request->request_status = $request->res;
        $assignment_request->target_staff_id = $targetStaffId;

        $assignment_request->update();

        /**
         *  Redirect user to district page
         */
        if ($assignment_request) {

            // Log user activity
            activity()->log('Accepted re(un)assignment request');

            // Get the requester (staff or paralegal)
            $requesterUser = null;
            if ($assignment_request->staff_id) {
                $staff = Staff::with('user.designation')
                    ->findOrFail((int) $assignment_request->staff_id);
                $requesterUser = $staff->user;
            } elseif ($assignment_request->requester_user_id) {
                $requesterUser = User::find($assignment_request->requester_user_id);
            }

            // Get the dispute
            $dispute = Dispute::findOrFail((int) $assignment_request->dispute_id) ?? NULL;
            $targetStaff = Staff::with('user.designation')
                ->findOrFail((int) $assignment_request->target_staff_id);

            // Always assign the dispute to the approved target staff, even if notifications are disabled
            $dispute->staff_id = $targetStaff->id;
            $dispute->save();

            // Staff infos
            $staff_display_name = '';
            if ($requesterUser) {
                $staff_title = trim((string) optional($requesterUser->designation)->name);
                $staff_name = trim(implode(' ', array_filter([
                    $requesterUser->first_name ?? '',
                    $requesterUser->middle_name ?? '',
                    $requesterUser->last_name ?? '',
                ])));
                $staff_display_name = $staff_title !== '' && strtolower($staff_title) !== 'other'
                    ? trim($staff_title . ' ' . $staff_name)
                    : $staff_name;
            }

            $staff_dest_addr = $requesterUser ? SmsService::normalizeRecipient($requesterUser->tel_no) : null;
            $staff_recipients = $staff_dest_addr ? ['recipient_id' => 1, 'dest_addr' => $staff_dest_addr] : null;

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

                    # Notify staff who made the request via SMS
                    $sms = new SmsService();
                    if ($staff_recipients) {
                        $sms->sendSMS($staff_recipients, $staff_message);
                    }

                    // Database & email 

                    # Notify staff who made the request via email & DB
                    if ($requesterUser) {
                        Notification::send($requesterUser, new RequestAccepted($requesterUser, $dispute, $staff_message));
                    }

                    // If there's a target staff, also notify them about the new assignment
                    if ($assignment_request->target_staff_id) {
                        $target_staff_title = trim((string) optional($targetStaff->user->designation)->name);
                        $target_staff_name = trim(implode(' ', array_filter([
                            $targetStaff->user->first_name ?? '',
                            $targetStaff->user->middle_name ?? '',
                            $targetStaff->user->last_name ?? '',
                        ])));
                        $target_staff_display_name = $target_staff_name;
                        if ($target_staff_title !== '' && strtolower($target_staff_title) !== 'other') {
                            $target_staff_display_name = trim($target_staff_title . ' ' . $target_staff_name);
                        }

                        $target_staff_dest_addr = SmsService::normalizeRecipient($targetStaff->user->tel_no);
                        $target_staff_recipients = ['recipient_id' => 1, 'dest_addr' => $target_staff_dest_addr];

                        $target_staff_message = 'Habari, ' . $target_staff_display_name .
                            ', AJISO inapenda kukutaarifu kuwa, shauri lenye namba ya usajili No. ' . $dispute->dispute_no . 
                            ' limekupewa. Tembelea Mfumo wa ALAS kujua zaidi. Ahsante.';

                        $sms->sendSMS($target_staff_recipients, $target_staff_message);
                        Notification::send($targetStaff->user, new \App\Notifications\StaffDisputeAssigned($targetStaff, $dispute, $target_staff_message));
                    }
                }
            } catch (\Throwable $th) {
                throw $th;
            }

            return redirect()->to(route('disputes.request.list', app()->getLocale(), false))
                ->with('status', 'You approved a reassignment request successfully.');
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


            // Get the requester (staff or paralegal)
            $requesterUser = null;
            if ($assignment_request->staff_id) {
                $staff = Staff::with('user.designation')
                    ->findOrFail((int) $assignment_request->staff_id);
                $requesterUser = $staff->user;
            } elseif ($assignment_request->requester_user_id) {
                $requesterUser = User::find($assignment_request->requester_user_id);
            }

            // Get the dispute
            $dispute = Dispute::findOrFail((int) $assignment_request->dispute_id) ?? NULL;

            // Staff infos
            $staff_display_name = '';
            if ($requesterUser) {
                $staff_title = trim((string) optional($requesterUser->designation)->name);
                $staff_name = trim(implode(' ', array_filter([
                    $requesterUser->first_name ?? '',
                    $requesterUser->middle_name ?? '',
                    $requesterUser->last_name ?? '',
                ])));
                $staff_display_name = $staff_title !== '' && strtolower($staff_title) !== 'other'
                    ? trim($staff_title . ' ' . $staff_name)
                    : $staff_name;
            }

            $staff_dest_addr = $requesterUser ? SmsService::normalizeRecipient($requesterUser->tel_no) : null;
            $staff_recipients = $staff_dest_addr ? ['recipient_id' => 1, 'dest_addr' => $staff_dest_addr] : null;

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
                    if ($staff_recipients) {
                        $sms->sendSMS($staff_recipients, $staff_message);
                    }

                    // Database & email 

                    # Notify new assigned assigned via email & DB
                    if ($requesterUser) {
                        Notification::send($requesterUser, new RequestRejected($requesterUser, $dispute, $staff_message));
                    }
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
