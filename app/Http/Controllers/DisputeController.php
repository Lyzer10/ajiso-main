<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Staff;
use App\Models\Dispute;
use App\Models\TypeOfCase;
use App\Models\Beneficiary;
use Illuminate\Support\Str;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Models\DisputeStatus;
use App\Models\TypeOfService;
use App\Models\DisputeActivity;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Notifications\DisputeCreated;
use App\Notifications\StaffDisputeAssigned;
use App\Notifications\ClientDisputeAssigned;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StaffDisputeUnassigned;

class DisputeController extends Controller
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
        // Check if current user is an authenticated staff
        if (Gate::denies('isStaff')) {
            // Get all disputes and bind them to the index view
            $query = Dispute::has('reportedBy')
                ->with(
                    'assignedTo:first_name,middle_name,last_name,user_no',
                    'reportedBy:first_name,middle_name,last_name,user_no',
                    'disputeStatus:id,dispute_status'
                )
                ->select(
                    [
                        'id',
                        'dispute_no',
                        'beneficiary_id',
                        'staff_id',
                        'reported_on',
                        'dispute_status_id',
                        'type_of_case_id'
                    ]
                )
                ->latest();

            // Filter by dispute status if provided
            if ($statusId = request('status')) {
                $query->where('dispute_status_id', (int) $statusId);
            }

            // Filter by case type if provided
            if ($caseTypeId = request('case_type')) {
                $query->where('type_of_case_id', (int) $caseTypeId);
            }

            // Filter by period if provided
            [$periodStart, $periodEnd] = $this->resolvePeriodRange(request('period'), request('dateRange'));
            if ($periodStart && $periodEnd) {
                $query->whereBetween('reported_on', [$periodStart, $periodEnd]);
            }

            // Search by beneficiary name
            if ($search = request('search')) {
                $query->whereHas('reportedBy', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            }

            $disputes = $query->paginate(10);
            $dispute_statuses = DisputeStatus::get(['id', 'dispute_status']);
            $type_of_cases = TypeOfCase::get(['id', 'type_of_case']);
        } else {
            return response('You are not authorized to perform this action!', 403);
        }

        //return $disputes;
        return response(view('disputes.list', compact('disputes', 'dispute_statuses', 'type_of_cases')));
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
                ->findOrFail(auth()->user()->id)
                ->staff->id ?? null;

            // Build the query for disputes
            $query = Dispute::has('reportedBy')
                ->with(
                    'reportedBy:first_name,middle_name,last_name,user_no',
                    'typeOfService:id,type_of_service',
                    'typeOfCase:id,type_of_case',
                    'disputeStatus:id,dispute_status'
                )
                ->where('staff_id', $staff)
                ->select([
                    'id',
                    'dispute_no',
                    'beneficiary_id',
                    'reported_on',
                    'type_of_service_id',
                    'type_of_case_id',
                    'dispute_status_id'
                ])
                ->latest();

            // Apply status filter if present
            $status = request('status'); // e.g., ?status=resolved
            if ($status === 'assigned') {
                $query->where('dispute_status_id', 1);
            } elseif ($status === 'proceeding') {
                $query->where('dispute_status_id', 2);
            } elseif ($status === 'resolved') {
                $query->where('dispute_status_id', 3);
            }

            // Search by beneficiary name
            if ($search = request('search')) {
                $query->whereHas('reportedBy', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                });
            }

            $disputes = $query->paginate(10);
        } else {
            return redirect()->back()->withErrors('You are not authorized to perform this action!');
        }

        return view('disputes.my-list', compact('disputes', 'status'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get all the beneficiaries and bind them to the create  view
        $beneficiaries = Beneficiary::has('user')
            ->with('user')
            ->latest()
            ->get(['id', 'user_id']);

        // Get all the type_of_services and bind them to the create  view
        $type_of_services = TypeOfService::latest()
            ->get(['id', 'type_of_service']);

        // Get all the type_of_cases and bind them to the create  view
        $type_of_cases = TypeOfCase::latest()
            ->get(['id', 'type_of_case']);

        return response(view('disputes.create-new', compact(
            'beneficiaries',
            'type_of_services',
            'type_of_cases'
        )));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectArchived()

    {
        // Get all disputes and bind them to the index view
        $disputes = Dispute::has('reportedBy')
            ->with(
                'reportedBy:first_name,middle_name,last_name,user_no',
                'typeOfService:id,type_of_service',
                'typeOfCase:id,type_of_case',
                'disputeStatus:id,dispute_status'
            )
            ->latest()
            ->take(10)
            ->get([
                'id',
                'dispute_no',
                'beneficiary_id',
                'reported_on',
                'type_of_service_id',
                'type_of_case_id',
                'dispute_status_id'
            ]);

        //return $disputes;
        return response(view('disputes.select-archive', compact('disputes')));
    }

    /**
     * Search a newly created resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function searchArchived(Request $request)
    {
        /**
         * Get a validator for an incoming search request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'dispute' => ['required', 'string', 'max:255'],
        ]);

        $disputes = [];

        $search = $request->dispute;

        if ($search && !is_null($search)) {

            $disputes = Dispute::whereHas('reportedBy', function ($query) use ($search) {
                $query->where('disputes.id', 'Like', '%' . $search . '%');
            })
                ->with('reportedBy', 'disputeStatus')
                ->latest()
                ->get(
                    [
                        'id',
                        'dispute_no',
                        'beneficiary_id',
                        'staff_id',
                        'reported_on',
                        'type_of_case_id',
                        'dispute_status_id'
                    ]
                );
        }

        if ($disputes) {

            //return $disputes;
            return response(view('disputes.confirm-archive', compact('disputes'))->with('status', 'Matches for "' . $search . '" found'));
        } else {

            //return $disputes with error;
            return response(
                redirect()->back()
                    ->withErrors('errors', 'No matches for "' . $search . '" found, please try again')
            );
        }
    }

    /**
     * Live search a newly created resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function liveSearchArchived(Request $request)
    {
        /**
         * Get a validator for an incoming search request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */

        $this->validate($request, [
            'q' => ['required', 'string', 'max:255'],
        ]);

        $disputes = [];

        if ($request->has('q')) {

            $search = $request->q;

            if ($search && !is_null($search)) {

                $disputes = Dispute::whereHas('reportedBy', function ($query) use ($search) {
                    $query->where('user_no', 'like', '%' . $search . '%')
                        ->orWhere('dispute_no', 'Like', '%' . $search . '%');
                })
                    ->with('reportedBy', 'disputeStatus')
                    ->latest()
                    ->get([
                        'id',
                        'dispute_no',
                        'beneficiary_id',
                        'reported_on',
                        'dispute_status_id'
                    ]);
            }
        }

        if ($disputes) {

            //return $disputes;
            return response()->json($disputes);
        } else {

            //return $disputes with error;
            return response(
                redirect()->back()
                    ->withErrors('errors', 'No matches for "' . $search . '" found, please try again')
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createArchived($locale, $id)
    {
        // Find dispute information by Id and return a edit view
        $dispute = Dispute::has('reportedBy')
            ->with('assignedTo', 'reportedBy', 'disputeStatus')
            ->findOrFail($id);

        // Get all the beneficiaries and bind them to the create  view
        $beneficiaries = Beneficiary::has('user')
            ->with('user')
            ->latest()
            ->get(['id', 'user_id']);

        // Get all the staff and bind them to the create  view
        $staff = Staff::has('user')
            ->with('user')
            ->latest()
            ->get(['id', 'user_id', 'center_id']);

        // Get all the dispute_statuses and bind them to the create  view
        $dispute_statuses = DisputeStatus::latest()
            ->get(['id', 'dispute_status']);

        // Get all the type_of_services and bind them to the create  view
        $type_of_services = TypeOfService::latest()
            ->get(['id', 'type_of_service']);

        // Get all the type_of_cases and bind them to the create  view
        $type_of_cases = TypeOfCase::latest()
            ->get(['id', 'type_of_case']);

        return response(view('disputes.create-archived', compact(
            'dispute',
            'staff',
            'type_of_services',
            'type_of_cases',
            'dispute_statuses'
        )));
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
            'dispute_no' => ['required', 'string', 'max:255'],
            'beneficiary' => ['required'],
            'type_of_service' => ['required'],
            'type_of_case' => ['required'],
            'reported_on' => ['required'],
            'matter_to_court' => ['required'],
            'type_of_court' => ['nullable'],
            'problem_description' => ['required', 'string'],
            'where_reported' => ['required', 'string', 'max:250'],
            'how_did_they_help' => ['nullable', 'max:500'],
            'service_experience' => ['nullable', 'max:500'],
            'how_can_we_help' => ['required', 'string'],
            'defendant_names_addr' => ['nullable', 'string'],
        ]);

        /**
         * Create a new dispute instance for a valid registration.
         *
         * @param  array  $dispute
         * @return \App\Models\Dispute
         */

        $dispute = new Dispute;

        $dispute->dispute_no = $request->dispute_no;
        $dispute->reported_on = Carbon::parse($request->reported_on)->format('Y-m-d');
        $dispute->beneficiary_id = (int) $request->beneficiary;
        $dispute->staff_id = NULL;
        $dispute->type_of_service_id = (int) $request->type_of_service;
        $dispute->type_of_case_id = (int) $request->type_of_case;
        $dispute->dispute_status_id = 1;
        $dispute->matter_to_court = $request->matter_to_court;
        $dispute->problem_description = $request->problem_description;
        $dispute->where_reported = $request->where_reported;
        $dispute->service_experience = $request->service_experience;
        $dispute->how_did_they_help = $request->how_did_they_help;
        $dispute->how_can_we_help = $request->how_can_we_help;
        $dispute->defendant_names_addr = $request->defendant_names_addr;

        /**
         * Save the dispute to the database
         */

        $dispute->save();

        /**
         *  Redirect user to disputes list
         */

        if ($dispute) {

            // Log user activity
            activity()->log('Registered dispute information');

            // Register dispute activity on dispute creation
            $activity = new DisputeActivity();

            $activity->dispute_activity = 'Dispute Reported';
            $activity->description = '';
            $activity->dispute_id = $dispute->id;

            // Store Dispute activity
            $activity->save();

            if ($activity) {

                $beneficiary = Beneficiary::has('user')
                    ->with(
                        'user:id,email,tel_no,first_name,middle_name,last_name,salutation_id'
                    )
                    ->select(['id', 'user_id', 'created_at'])
                    ->findOrFail($request->beneficiary);

                // Send SMS both legal aid provider and beneficiary
                $dest_addr = SmsService::normalizeRecipient($beneficiary->user->tel_no);
                $recipients = ['recipient_id' => 1, 'dest_addr' => $dest_addr];

                $title = trim((string) optional($beneficiary->user->designation)->name);
                $full_name = trim(implode(' ', array_filter([
                    $beneficiary->user->first_name ?? '',
                    $beneficiary->user->middle_name ?? '',
                    $beneficiary->user->last_name ?? '',
                ])));
                $display_name = $full_name;
                if ($title !== '' && strtolower($title) !== 'other') {
                    $display_name = trim($title . ' ' . $full_name);
                }
                $beneficiary_no = $beneficiary->user->user_no;
                $created_at = Carbon::parse($beneficiary->created_at)->format('d/m/Y');

                $message = 'Habari, ' . $display_name .
                    ', AJISO inapenda kukutaarifu kuwa, Kesi yako yenye namba ya usajili No.' . $dispute->dispute_no .
                    ' imefanikiwa kusajiliwa leo, ' . $created_at .
                    '. Ahsante.';

                /**
                 * Send sms, email & database notification
                 */

                try {
                    if (env('SEND_NOTIFICATIONS') == TRUE) {
                        // SMS
                        $sms = new SmsService();
                        $sms->sendSMS($recipients, $message);

                        // Database & email
                        Notification::send($beneficiary->user, new DisputeCreated($beneficiary, $dispute, $message));
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }
            }

            return response(
                redirect()->route('disputes.list', app()->getLocale())
                    ->with('status', 'Dispute information added, successfully.')
            );
        } else {
            return response(
                redirect()->back()
                    ->withErrors('errors', 'Adding dispute information failed, please try again.')
            );
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeArchived(Request $request)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'dispute_no' => ['required', 'string', 'max:255'],
            'beneficiary' => ['required'],
            'type_of_service' => ['required'],
            'type_of_case' => ['required'],
            'reported_on' => ['max:20'],
            'matter_to_court' => ['required'],
            'problem_description' => ['required', 'string'],
            'where_reported' => ['required', 'string', 'max:255'],
            'how_did_they_help' => ['nullable', 'max:500'],
            'service_experience' => ['nullable', 'max:500'],
            'how_can_we_help' => ['required', 'string'],
            'defendant_names_addr' => ['nullable', 'string'],
        ]);

        /**
         * Create a new dispute instance for a valid registration.
         *
         * @param  array  $dispute
         * @return \App\Models\Dispute
         */

        $dispute = new Dispute;

        $dispute->dispute_no = $request->dispute_no;

        if (!is_null($request->reported_on)) {
            $dispute->reported_on = Carbon::parse($request->reported_on)->format('Y-m-d');
        } else {
            $dispute->reported_on = Carbon::parse(now())->format('Y-m-d');
        }

        $dispute->beneficiary_id = (int) $request->beneficiary;
        $dispute->staff_id = NULL;
        $dispute->type_of_service_id = $request->type_of_service;
        $dispute->type_of_case_id = $request->type_of_case;
        $dispute->dispute_status_id = 4;
        $dispute->matter_to_court = $request->matter_to_court;
        $dispute->problem_description = $request->problem_description;
        $dispute->where_reported = $request->where_reported;
        $dispute->service_experience = $request->service_experience;
        $dispute->how_did_they_help = $request->how_did_they_help;
        $dispute->how_can_we_help = $request->how_can_we_help;
        $dispute->defendant_names_addr = $request->defendant_names_addr;

        /**
         * Save the dispute to the database
         */

        $dispute->save();

        /**
         *  Redirect user to disputes list
         */

        if ($dispute) {

            // Log user activity
            activity()->log('Registered dispute information');

            // Register dispute activity on dispute creation
            $activity = new DisputeActivity();

            $activity->dispute_activity = 'Dispute Reported';
            $activity->description = '';
            $activity->dispute_id = (int) $dispute->id;
            $activity->save();

            // Check if activity saved successfully
            if ($activity) {

                // Query beneficiary
                $beneficiary = Beneficiary::has('user')
                    ->with(
                        'user:id,tel_no,first_name,middle_name,last_name,salutation_id'
                    )
                    ->select(['id', 'user_id', 'created_at'])
                    ->findOrFail($request->beneficiary);

                // Send SMS both legal aid provider and beneficiary
                $dest_addr = SmsService::normalizeRecipient($beneficiary->user->tel_no);
                $recipients = ['recipient_id' => 1, 'dest_addr' => $dest_addr];

                $title = trim((string) optional($beneficiary->user->designation)->name);
                $full_name = trim(implode(' ', array_filter([
                    $beneficiary->user->first_name ?? '',
                    $beneficiary->user->middle_name ?? '',
                    $beneficiary->user->last_name ?? '',
                ])));
                $display_name = $full_name;
                if ($title !== '' && strtolower($title) !== 'other') {
                    $display_name = trim($title . ' ' . $full_name);
                }
                $created_at = Carbon::parse($beneficiary->created_at)->format('d/m/Y');

                $message = 'Habari, ' . $display_name .
                    ', AJISO inapenda kukutaarifu kuwa, Kesi yako yenye namba ya usajili No.' . $dispute->dispute_no .
                    ' imefanikiwa kusajiliwa leo, ' . $created_at .
                    '. Ahsante.';
                /**
                 * Send sms, email & database notification
                 */
                try {
                    if (env('SEND_NOTIFICATIONS') == TRUE) {
                        // SMS
                        $sms = new SmsService();
                        $sms->sendSMS($recipients, $message);

                        // Database & email
                        Notification::send($beneficiary->user, new DisputeCreated($beneficiary, $dispute, $message));
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }
            }

            return response(
                redirect()->route('disputes.list', app()->getLocale())
                    ->with('status', 'Dispute information added, successfully.')
            );
        } else {
            return response(
                redirect()->back()
                    ->withErrors('errors', 'Adding dispute information failed, please try again.')
            );
        }
    }

    /**
     * Show the form for creating a assigning resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function assign($locale, $id)
    {
        if ($id === 'all' && !is_numeric($id)) {

            $disputes = Dispute::has('reportedBy')
                ->with('reportedBy', 'assignedTo')
                ->latest()
                ->get([
                    'id',
                    'dispute_no',
                    'beneficiary_id',
                    'staff_id',
                    'dispute_status_id',
                    'reported_on'
                ]);

            // create an empty collection to help render view logic
            $dispute = collect();

            // Get all the staff and bind them to the create  view
            $staff = Staff::has('user')->with('user', 'center')->get(['id', 'user_id', 'center_id']);

            // return view compacted with dispute(s) and staff info
            return response(view('disputes.assignment', compact('disputes', 'dispute', 'staff')));
        } else {
            // Find dispute information by Id and return a edit view
            $dispute = Dispute::has('reportedBy')
                ->with('reportedBy', 'assignedTo')
                ->findOrFail($id);

            // create an empty collection to help render view logic
            $disputes = collect();

            // Get all the staff and bind them to the create  view
            $staff = Staff::has('user')->with('user', 'center')->get(['id', 'user_id', 'center_id']);

            // return view compacted with dispute(s) and staff info
            return response(view('disputes.assignment', compact('dispute', 'disputes', 'staff')));
        }
    }

    /**
     * Bind a LAP to a dispute.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bindDispute(Request $request)
    {
        /**
         * Get a validator for an incoming store request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */
        $this->validate($request, [
            'dispute' => ['required'],
            'staff' => ['required'],
        ]);

        /**
         * Create a new dispute instance for a valid registration.
         *
         * @param  array  $dispute
         * @return \App\Models\Dispute
         */

        $dispute = Dispute::select(
            [
                'id',
                'staff_id',
                'beneficiary_id',
                'dispute_no',
                'dispute_status_id',
                'reported_on'
            ]
        )
            ->findOrFail($request->dispute);

        $dispute_last_staff = $dispute->staff_id;

        if ($request->staff == 'null') {
            $dispute->staff_id = NULL;
            $dispute->dispute_status_id = 1;
        } else {
            $dispute->staff_id = (int) $request->staff;
            $dispute->dispute_status_id = 2;
        }

        /**
         * Save the dispute to the database
         */

        $dispute->update();

        /**
         *  Redirect user to disputes list
         */

        if ($dispute) {

            // Log user activity
            activity()->log('Assigned dispute to legal aid provider');

            // Register dispute activity on dispute is_assigned or re-is_assigned
            $activity = new DisputeActivity();

            // Check if staff is assigned or null if not
            if (!is_null($request->staff)) {

                // Get full name of the staff
                $staff_assigned = Staff::has('user')
                    ->with(
                        'user:id,email,tel_no,first_name,middle_name,last_name,salutation_id'
                    )
                    ->select(['id', 'user_id', 'is_assigned'])
                    ->findOrFail($dispute->staff_id);

                //Change the status of the staff from unassigned to assigned
                $staff_assigned->is_assigned = (int) true;

                $staff_assigned->update();

                // Prepare the dispute activity information
                $staff_name = trim(implode(' ', array_filter([
                    $staff_assigned->user->first_name,
                    $staff_assigned->user->middle_name,
                    $staff_assigned->user->last_name,
                ])));
                $staff_title = trim((string) optional($staff_assigned->user->designation)->name);

                $activity->dispute_activity = 'Dispute Assigned';
                $activity->description = 'Legal Aid Provider : ' . $staff_name;
                $activity->dispute_id = $dispute->id;
            } else {

                // Record dispute activity

                $activity->dispute_activity = 'Dispute Unassigned';
                $activity->description = 'Legal Aid Provider : N/A';
                $activity->dispute_id = $dispute->id;
            }

            // Finally save the activity information to the database
            $activity->save();

            // Check if activity saved successfully
            if ($activity) {
                // Get beneficiary information
                $beneficiary = Beneficiary::has('user')
                    ->with(
                        'user:id,email,tel_no,first_name,middle_name,last_name,salutation_id'
                    )
                    ->select(['id', 'user_id'])
                    ->findOrFail($dispute->beneficiary_id);

                // Get full name of the beneficiary
                $beneficiary_name = trim(implode(' ', array_filter([
                    $beneficiary->user->first_name,
                    $beneficiary->user->middle_name,
                    $beneficiary->user->last_name,
                ])));
                $beneficiary_title = trim((string) optional($beneficiary->user->designation)->name);

                $formatDisplayName = function ($title, $name) {
                    $title = trim((string) $title);
                    if ($title === '' || strtolower($title) === 'other') {
                        return $name;
                    }
                    return trim($title.' '.$name);
                };

                $beneficiary_display_name = $formatDisplayName($beneficiary_title, $beneficiary_name);
                $staff_display_name = $formatDisplayName($staff_title, $staff_name);

                // Get date when the case was registered
                $reported_on = Carbon::parse($dispute->reported_on)->format('d/m/Y');

                // Get date when the case was assigned
                $assigned_at = Carbon::now()->format('d/m/Y');

                // Beneficiary
                $dest_addr = SmsService::normalizeRecipient($beneficiary->user->tel_no);
                $recipients = ['recipient_id' => 1, 'dest_addr' => $dest_addr];

                $message = 'Habari, ' . $beneficiary_display_name .
                    ', AJISO inapenda kukutaarifu kuwa, shauri lako uliloripoti tarehe ' . $reported_on .
                    ' lenye namba ya usajili No. ' . $dispute->dispute_no .
                    ' limepangiwa mtoa msaada wa kisheria ' . $staff_display_name .
                    ' leo, ' . $assigned_at . '.' .
                    ' Ahsante.';

                // Staff
                $staff_dest_addr = SmsService::normalizeRecipient($staff_assigned->user->tel_no);
                $staff_recipients = ['recipient_id' => 1, 'dest_addr' => $staff_dest_addr];

                $staff_message = 'Habari, ' . $staff_display_name .
                    ', AJISO inapenda kukutaarifu kuwa, shauri lililoripotiwa tarehe ' . $reported_on .
                    ' lenye namba ya usajili No. ' . $dispute->dispute_no .
                    ' na ' . $beneficiary_display_name .
                    ' limepangiwa kwako leo, ' . $assigned_at .
                    '. Tembelea Mfumo wa ALAS kujua zaidi.' .
                    ' Ahsante.';


                if (!is_null($dispute_last_staff) && !is_null($request->staff)) {

                    // Get full name of the staff
                    $last_assigned_staff = Staff::has('user')
                        ->with(
                            'user:id,email,tel_no,first_name,middle_name,last_name,salutation_id'
                        )
                        ->select(['id', 'user_id', 'is_assigned'])
                        ->findOrFail($dispute_last_staff);

                    // Staff infos

                    $last_staff_name = trim(implode(' ', array_filter([
                        $last_assigned_staff->user->first_name,
                        $last_assigned_staff->user->middle_name,
                        $last_assigned_staff->user->last_name,
                    ])));
                    $last_staff_title = trim((string) optional($last_assigned_staff->user->designation)->name);
                    $last_staff_display_name = $formatDisplayName($last_staff_title, $last_staff_name);
                    $last_staff_dest_addr = SmsService::normalizeRecipient($last_assigned_staff->user->tel_no);
                    $last_staff_recipients = ['recipient_id' => 1, 'dest_addr' => $last_staff_dest_addr];

                    $last_staff_message = 'Habari, ' . $last_staff_display_name .
                        ', AJISO inapenda kukutaarifu kuwa, shauri' .
                        ' lenye namba ya usajili No. ' . $dispute->dispute_no .
                        ' ya ' . $beneficiary_display_name .
                        ' ulilokuwa unalishughulikia limepangiwa mtoa huduma mwingine leo, ' .
                        $assigned_at . '.' .
                        ' Ahsante.';

                    //Send SMS, Email & Database notifications to both legal aid provider and beneficiary

                    /**
                     * Send sms, email & database notification
                     */

                    try {
                        // SMS
                        if (env('SEND_NOTIFICATIONS') == TRUE) {
                            // Notify Beneficiary via SMS
                            $sms = new SmsService();
                            $sms->sendSMS($recipients, $message);

                            # Notify new assigned assigned via SMS
                            $staff_sms = new SmsService();
                            $staff_sms->sendSMS($staff_recipients, $staff_message);

                            # Notify last assigned staff via SMS
                            $last_staff_sms = new SmsService();
                            $last_staff_sms->sendSMS($last_staff_recipients, $last_staff_message);

                            // Database & email 

                            # Notify beneficiary
                            Notification::send($beneficiary->user, new ClientDisputeAssigned($beneficiary, $dispute, $message));

                            # Notify new assigned assigned via email & DB
                            Notification::send($staff_assigned->user, new StaffDisputeAssigned($staff_assigned, $dispute, $staff_message));


                            # Notify last assigned staff via email & DB
                            Notification::send($last_assigned_staff->user, new StaffDisputeUnassigned($last_assigned_staff, $dispute, $last_staff_message));
                        }
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                } else if (is_null($dispute_last_staff) && !is_null($request->staff)) {

                    //Send SMS, Email & Database notifications to both legal aid provider and beneficiary

                    /**
                     * Send sms, email & database notification
                     */

                    try {
                        // SMS
                        if (env('SEND_NOTIFICATIONS') == TRUE) {
                            // Notify Beneficiary via SMS
                            $sms = new SmsService();
                            $sms->sendSMS($recipients, $message);

                            # Notify new assigned assigned via SMS
                            $staff_sms = new SmsService();
                            $staff_sms->sendSMS($staff_recipients, $staff_message);

                            // Database & email 

                            # Notify beneficiary
                            Notification::send($beneficiary->user, new ClientDisputeAssigned($beneficiary, $dispute, $message));

                            # Notify new assigned assigned via email & DB
                            Notification::send($staff_assigned->user, new StaffDisputeAssigned($staff_assigned, $dispute, $staff_message));
                        }
                    } catch (\Throwable $th) {
                        throw $th;
                    }
                }
            }

            return response(
                redirect()->route('disputes.list', app()->getLocale())
                    ->with('status', 'Dispute assigned to legal aid provider, successfully.')
            );
        } else {
            return response(
                redirect()->back()
                    ->withErrors('errors', 'Assigning dispute to legal aid provider failed, please try again.')
            );
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
        //TODO : Refactor eager loading models for optimizing queries
        //Find dispute information by Id and return a profile view
        $dispute = Dispute::with(
            'assignedTo',
            'reportedBy',
            'disputeStatus',
            'counselingSheets',
            'attachments'
        )->findOrFail($id);

        // TODO : Try the only() method on the above models within the show view
        // Get how many times a dispute has been reported
        $occurrences = Dispute::with('assignedTo', 'disputeStatus')
            ->where('dispute_no', $dispute->dispute_no)
            ->get(['id', 'reported_on', 'staff_id', 'dispute_status_id']);

        // Get all the dispute_statuses and bind them to the create  view
        $dispute_statuses = DisputeStatus::get(['id', 'dispute_status']);

        return response(view('disputes.show', compact('dispute', 'occurrences', 'dispute_statuses')));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id,
     * @return \Illuminate\Http\Response
     */
    public function edit($locale, $id)
    {
        // Find dispute information by Id and return a edit view
        $dispute = Dispute::with('assignedTo', 'reportedBy', 'disputeStatus')->findOrFail($id);

        // Get all the type_of_services and bind them to the create  view
        $type_of_services = TypeOfService::get(['id', 'type_of_service']);

        // Get all the type_of_cases and bind them to the create  view
        $type_of_cases = TypeOfCase::get(['id', 'type_of_case']);

        // Get all the dispute_statuses and bind them to the create  view
        $dispute_statuses = DisputeStatus::get(['id', 'dispute_status']);

        //return $disputes;
        return response(view('disputes.edit', compact(
            'dispute',
            'type_of_services',
            'type_of_cases',
            'dispute_statuses'
        )));
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
            'beneficiary' => ['required'],
            'type_of_service' => ['required'],
            'type_of_case' => ['required'],
            'dispute_status' => ['required'],
            'matter_to_court' => ['required'],
            'problem_description' => ['required', 'string', 'max:255'],
            'where_reported' => ['required', 'string', 'max:255'],
            'how_did_they_help' => ['nullable', 'max:500'],
            'service_experience' => ['nullable', 'max:500'],
            'how_can_we_help' => ['required', 'string'],
            'defendant_names_addr' => ['nullable', 'string'],
        ]);

        /**
         * Create a new dispute instance for a valid registration.
         *
         * @param  array  $dispute
         * @return \App\Models\Dispute
         */

        $dispute = Dispute::findOrFail($id);

        $dispute->reported_on = Carbon::parse($request->reported_on)->format('Y-m-d');
        $dispute->beneficiary_id = $request->beneficiary;
        $dispute->type_of_service_id = $request->type_of_service;
        $dispute->type_of_case_id = $request->type_of_case;
        $dispute->matter_to_court = $request->matter_to_court;
        $dispute->problem_description = $request->problem_description;
        $dispute->where_reported = $request->where_reported;
        $dispute->service_experience = $request->service_experience;
        $dispute->how_did_they_help = $request->how_did_they_help;
        $dispute->how_can_we_help = $request->how_can_we_help;
        $dispute->defendant_names_addr = $request->defendant_names_addr;

        /**
         * Save the dispute to the database
         */

        $dispute->update();

        /**
         *  Redirect user to disputes list
         */

        if ($dispute) {

            // Log user activity
            activity()->log('Updated dispute information');

            return response(redirect()->back()
                ->with('status', 'Dispute information updated, successfully.'));
        } else {
            return response(redirect()->back()
                ->withErrors('errors', 'Updating dispute information failed, please try again.'));
        }
    }

    /**
     * Remove the specified disputes from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trash($locate, $id)
    {
        //Deleting dispute information from the database

        $dispute = Dispute::findOrFail($id);

        $dispute->delete();

        if ($dispute) {

            // Log user activity
            activity()->log('Trashed dispute account');

            return redirect()->back()
                ->with('status', 'Dispute information trashed, successfully.');
        } else {
            return redirect()->back()
                ->withErrors('errors', 'Trashing dispute information failed, please try again.');
        }
    }

    /**
     * Restoring the specified disputes from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($locate, $id)
    {
        //Restoring dispute information from the database

        $dispute = Dispute::onlyTrashed()->findOrFail($id);

        $dispute->restore();

        if ($dispute) {

            // Log user activity
            activity()->log('Restored dispute account');

            return response(
                redirect()->back()
                    ->with('status', 'Dispute information restored, successfully.')
            );
        } else {
            return response(
                redirect()->back()
                    ->withErrors('errors', 'Restoring dispute information failed, please try again.')
            );
        }
    }

    /**
     * Remove the specified disputes from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($locate, $id)
    {
        //Deleting dispute information from the database

        $dispute = Dispute::onlyTrashed()->findOrFail($id);

        $dispute->forceDelete();

        if ($dispute) {

            // Log user activity
            activity()->log('Deleted dispute information');

            return response(redirect()->back()
                ->with('status', 'Dispute information deleted, successfully.'));
        } else {
            return response(redirect()->back()
                ->withErrors('errors', 'Deleting dispute information failed, please try again.'));
        }
    }

    private function resolvePeriodRange($period, $dateRange)
    {
        $period = $period ?: ($dateRange ? 'custom' : null);
        if (!$period) {
            return [null, null];
        }

        $now = Carbon::now();

        switch ($period) {
            case 'today':
                return [$now->copy()->startOfDay()->format('Y-m-d'), $now->copy()->endOfDay()->format('Y-m-d')];
            case 'this_week':
                return [$now->copy()->startOfWeek()->format('Y-m-d'), $now->copy()->endOfWeek()->format('Y-m-d')];
            case 'this_month':
                return [$now->copy()->startOfMonth()->format('Y-m-d'), $now->copy()->endOfMonth()->format('Y-m-d')];
            case 'last_three_months':
                return [$now->copy()->subMonths(3)->startOfDay()->format('Y-m-d'), $now->copy()->endOfDay()->format('Y-m-d')];
            case 'this_year':
                return [$now->copy()->startOfYear()->format('Y-m-d'), $now->copy()->endOfYear()->format('Y-m-d')];
            case 'custom':
                if (!$dateRange || !Str::contains($dateRange, '-')) {
                    return [null, null];
                }
                $date = explode(' - ', $dateRange);
                if (count($date) !== 2) {
                    return [null, null];
                }
                try {
                    $date_start = Carbon::parse($date[0])->format('Y-m-d');
                    $date_end = Carbon::parse($date[1])->format('Y-m-d');
                    return [$date_start, $date_end];
                } catch (\Throwable $th) {
                    return [null, null];
                }
            default:
                return [null, null];
        }
    }
}
