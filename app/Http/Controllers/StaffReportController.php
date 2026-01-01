<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Staff;
use App\Models\Dispute;
use App\Models\TypeOfCase;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use App\Models\DisputeStatus;
use App\Models\TypeOfService;
use App\Models\User;

class StaffReportController extends Controller
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
     *Get the current authenticated user
     */

    protected function getCurrentUser()
    {
        return User::with('staff')->findOrFail(auth()->user()->id)->staff->id;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $staff = $this->getCurrentUser();

        // Get all disputes and bind them to the index view
        $disputes = Dispute::with('reportedBy:first_name,middle_name,last_name',
                                    'disputeStatus', 'typeOfService','typeOfCase')
                            ->select(['id', 'dispute_no', 'reported_on', 'beneficiary_id',
                                        'type_of_service_id','type_of_case_id', 'dispute_status_id',
                            ])
                            ->where('staff_id', $staff)
                            ->latest()
                            ->paginate(10);

        // TODO replace staff id of $staff with auth()->staff_id

        // Get count of resolved disputes
        $resolved_count = Dispute::where('dispute_status_id', $staff)
                                    ->where('staff_id', $staff)
                                    ->count();

        // Get all the beneficiaries associated to staff and bind them to the create  view
        $beneficiaries = Dispute::with('reportedBy:first_name,middle_name,last_name,user_no')
                                ->select(Dispute::raw('DISTINCT beneficiary_id'))
                                ->where('staff_id', $staff)
                                ->latest()
                                ->get();

        // return $beneficiaries;

        // Get all the dispute_statuses and bind them to the create  view
        $dispute_statuses =DisputeStatus::latest()
                                        ->get(['id','dispute_status']);

        // Get all the type_of_services and bind them to the create  view
        $type_of_services = TypeOfService::latest()
                                        ->get(['id','type_of_service']);

        // Get all the type_of_cases and bind them to the create  view
        $type_of_cases = TypeOfCase::latest()
                                    ->get(['id','type_of_case']);

        //return $disputes;
        return view('reports.staff.general',compact('disputes', 'beneficiaries',
                                                'type_of_services', 'type_of_cases',
                                                'dispute_statuses', 'resolved_count'));
    }

    /**
     * Search the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        /**
         * Get a validator for an incoming search request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */

        $this->validate($request, [
            'filterBy' => ['required', 'string', 'max:255'],
            'dateRange' => ['required', 'string', 'max:255'],
        ]);

        $staff = $this->getCurrentUser();

        $disputes = [];

        // split dateRange into $date_start and $date_end

        $date_raw = $request->dateRange;

        $date = explode(' - ', $date_raw);

        $date_start =  Carbon::parse($date[0])->format('Y-m-d');
        $date_end = Carbon::parse($date[1])->format('Y-m-d');

        if ($request->has('filterBy')) {

            $filter = $request->filterBy;

            if ($filter === 'allFilter') {

                // Validate individual filter value
                $this->validate($request, [
                    'all' => ['nullable', 'string', 'max:255'],
                ]);

                $search = $request->all;

                $disputes = Dispute::with('assignedTo', 'reportedBy', 'disputeStatus', 'typeOfCase')
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff)
                                    ->select(['id', 'dispute_no', 'beneficiary_id', 'staff_id', 'reported_on',
                                            'type_of_service_id','type_of_case_id', 'dispute_status_id'])
                                    ->latest()
                                    ->paginate(10);

                // Filter info to be displayed
                $filter_by = 'N/A';
                $filter_val = 'All';

            }elseif ($filter === 'beneficiaryFilter') {

                // Validate individual filter value
                $this->validate($request, [
                    'beneficiary' => ['required', 'numeric', 'max:255'],
                ]);

                $search = $request->beneficiary;

                $disputes = Dispute::with('assignedTo', 'reportedBy', 'disputeStatus', 'typeOfCase')
                                    ->where('beneficiary_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff)
                                    ->latest()
                                    ->select(['id', 'dispute_no', 'beneficiary_id', 'staff_id', 'reported_on',
                                                'type_of_service_id', 'type_of_case_id', 'dispute_status_id'])
                                    ->paginate(10);

                // Get full name of the search term
                $val = Beneficiary::with('User')
                                ->select(['id', 'user_id'])
                                ->findOrFail($search);

                $val = $val->user->first_name.' '.$val->user->middle_name.' '.$val->user->last_name;

                // filter info to be displayed
                $filter_by = 'Beneficiaries';
                $filter_val = $val;

            }elseif ($filter === 'tosFilter') {

                $this->validate($request, [
                    'type_of_service' => ['required', 'numeric', 'max:255'],
                ]);

                $search = $request->type_of_service;

                $disputes = Dispute::with('assignedTo', 'reportedBy', 'disputeStatus', 'typeOfCase')
                                    ->where('type_of_service_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff)
                                    ->latest()
                                    ->select(['id', 'dispute_no', 'beneficiary_id', 'staff_id', 'reported_on',
                                            'type_of_service_id','type_of_case_id', 'dispute_status_id'])
                                    ->paginate(10);

                // Get full name of the search term
                $val = TypeOfService::select(['id', 'type_of_service'])
                                    ->findOrFail($search);

                // filter info to be displayed
                $filter_by = 'Types of Services';
                $filter_val = $val->type_of_service;

            }elseif ($filter === 'tocFilter') {

                // validate individual filter value
                $this->validate($request, [
                    'type_of_case' => ['required', 'numeric', 'max:255'],
                ]);

                $search = $request->type_of_case;

                $disputes = Dispute::with('assignedTo', 'reportedBy', 'disputeStatus', 'typeOfCase')
                                    ->where('type_of_case_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff)
                                    ->latest()
                                    ->select(['id', 'dispute_no', 'beneficiary_id', 'staff_id', 'reported_on',
                                            'type_of_service_id','type_of_case_id', 'dispute_status_id'])
                                    ->paginate(10);

                // Get full name of the search term
                $val = TypeOfCase::select(['id', 'type_of_case'])
                                    ->findOrFail($search);

                // filter info to be displayed
                $filter_by = 'Types of Cases';
                $filter_val = $val->type_of_case;

            }elseif ($filter === 'statusFilter') {

                // validate individual filter value
                $this->validate($request, [
                    'dispute_status' => ['required', 'numeric', 'max:255'],
                ]);

                $search = $request->dispute_status;

                $disputes = Dispute::with('assignedTo', 'reportedBy', 'disputeStatus', 'typeOfCase')
                                    ->where('dispute_status_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff)
                                    ->latest()
                                    ->select(['id', 'dispute_no', 'beneficiary_id', 'staff_id', 'reported_on',
                                            'type_of_service_id','type_of_case_id', 'dispute_status_id'])
                                    ->paginate(10);

                // Get full name of the search term
                $val = DisputeStatus::select(['id', 'dispute_status'])
                                    ->findOrFail($search);

                // filter info to be displayed
                $filter_by = 'Dispute Statuses';
                $filter_val = $val->dispute_status;

            }else {
                // Redirect with error;
                return redirect()->back()
                                ->withErrors('errors', 'Something went wrong, please try again');
            }
        }

        if ($disputes) {

            // Get count of resolved disputes
            $resolved_count = $disputes->where('dispute_status_id', '$staff')
                                        ->count();

            //return $disputes;
            return view('reports.staff.general-results', compact('disputes', 'filter_by', 'filter_val',
                                                            'date_raw', 'resolved_count')
                                                        );

        }else {

            //return with error;
            return redirect()->back()
                            ->withErrors('errors', 'No matches for found, please try again');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function disputesSummary()
    {
        $staff = $this->getCurrentUser();

        $date_ranges = 'All';

        $total = Dispute::where('staff_id', $staff)
                        ->count();

        $group_by_service = Dispute::select('type_of_service_id', Dispute::raw('count(*) as total'))
                                    ->groupBy('type_of_service_id')
                                    ->where('staff_id', $staff)
                                    ->get();

        $group_by_case = Dispute::select('type_of_case_id', Dispute::raw('count(*) as total'))
                                ->groupBy('type_of_case_id')
                                ->where('staff_id', $staff)
                                ->get();

        $group_by_status = Dispute::select('dispute_status_id', Dispute::raw('count(*) as total'))
                                    ->groupBy('dispute_status_id')
                                    ->where('staff_id', $staff)
                                    ->get();

        // Get all the dispute_statuses
        $dispute_statuses =DisputeStatus::latest()
                                        ->get(['id','dispute_status']);

        // Get all the type_of_services
        $type_of_services = TypeOfService::latest()
                                            ->get(['id','type_of_service']);

        // Get all the type_of_cases
        $type_of_cases = TypeOfCase::latest()
                                    ->get(['id','type_of_case']);

        return view('reports.staff.case-summary', compact('total', 'date_ranges','group_by_service',
                                                    'group_by_case',
                                                    'group_by_status','dispute_statuses',
                                                    'type_of_services','type_of_cases'));

    }

    /**
     * Filter dispute details based on date ranges
     */

    public function disputesSummaryFilter(Request $request)
    {

        /**
         * Get a validator for an incoming search request.
         *
         * @param  array  $request
         * @return \Illuminate\Contracts\Validation\Validator
         */

        $this->validate($request, [
            'dateRange' => ['required', 'string', 'max:255'],
        ]);

        $staff = $this->getCurrentUser();

        // split dateRange into $date_start and $date_end

        $date_ranges = $request->dateRange;

        $date = explode(' - ', $date_ranges);

        $date_start =  Carbon::parse($date[0])->format('Y-m-d');
        $date_end = Carbon::parse($date[1])->format('Y-m-d');

        $total = Dispute::whereBetween('reported_on', [$date_start, $date_end])
                        ->where('staff_id', $staff)
                        ->count();

        $group_by_service = Dispute::select('type_of_service_id', Dispute::raw('count(*) as total'))
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff)
                                    ->groupBy('type_of_service_id')
                                    ->get();

        $group_by_case = Dispute::select('type_of_case_id', Dispute::raw('count(*) as total'))
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff)
                                    ->groupBy('type_of_case_id')
                                    ->get();

        $group_by_status = Dispute::select('dispute_status_id', Dispute::raw('count(*) as total'))
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff)
                                    ->groupBy('dispute_status_id')
                                    ->get();

        // Get all the dispute_statuses
        $dispute_statuses =DisputeStatus::latest()
                                        ->get(['id','dispute_status']);

        // Get all the type_of_services
        $type_of_services = TypeOfService::latest()
                                            ->get(['id','type_of_service']);

        // Get all the type_of_cases
        $type_of_cases = TypeOfCase::latest()
                                    ->get(['id','type_of_case']);

        return view('reports.staff.case-summary', compact('total', 'date_ranges','group_by_service',
                                                    'group_by_case', 'group_by_status','dispute_statuses',
                                                    'type_of_services', 'type_of_cases'));
    }

}
