<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Staff;
use App\Models\Dispute;
use App\Models\TypeOfCase;
use App\Models\AgeGroup;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use App\Models\DisputeStatus;
use App\Models\TypeOfService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
        $resolvedStatusId = DisputeStatus::whereRaw('LOWER(dispute_status) = ?', ['resolved'])
                                        ->value('id');

        // Get all disputes and bind them to the index view
        $disputes = Dispute::with('reportedBy:first_name,middle_name,last_name',
                                    'disputeStatus', 'typeOfService','typeOfCase')
                            ->select(['id', 'dispute_no', 'reported_on', 'beneficiary_id',
                                        'type_of_service_id','type_of_case_id', 'dispute_status_id',
                            ])
                            ->where('staff_id', $staff)
                            ->latest()
                            ->paginate(10);

        $totalCases = Dispute::where('staff_id', $staff)->count();

        $statusCounts = Dispute::select('dispute_status_id', DB::raw('count(*) as total'))
                                ->where('staff_id', $staff)
                                ->groupBy('dispute_status_id')
                                ->pluck('total', 'dispute_status_id');

        $resolved_count = $resolvedStatusId
            ? $statusCounts->get($resolvedStatusId, 0)
            : 0;

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
                                                'dispute_statuses', 'resolved_count',
                                                'statusCounts', 'totalCases'));
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

        $resolvedStatusId = DisputeStatus::whereRaw('LOWER(dispute_status) = ?', ['resolved'])
                                        ->value('id');

        $baseQuery = null;

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

                $baseQuery = Dispute::query()
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff);

                // Filter info to be displayed
                $filter_by = 'N/A';
                $filter_val = 'All';

            }elseif ($filter === 'beneficiaryFilter') {

                // Validate individual filter value
                $this->validate($request, [
                    'beneficiary' => ['required', 'numeric', 'max:255'],
                ]);

                $search = $request->beneficiary;

                $baseQuery = Dispute::query()
                                    ->where('beneficiary_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff);

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

                $baseQuery = Dispute::query()
                                    ->where('type_of_service_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff);

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

                $baseQuery = Dispute::query()
                                    ->where('type_of_case_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff);

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

                $baseQuery = Dispute::query()
                                    ->where('dispute_status_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->where('staff_id', $staff);

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

        if ($baseQuery) {

            $disputes = (clone $baseQuery)
                        ->with('assignedTo', 'reportedBy', 'disputeStatus', 'typeOfService', 'typeOfCase')
                        ->select(['id', 'dispute_no', 'beneficiary_id', 'staff_id', 'reported_on',
                                'type_of_service_id','type_of_case_id', 'dispute_status_id'])
                        ->latest()
                        ->paginate(10);

            $totalCases = (clone $baseQuery)->count();

            $statusCounts = (clone $baseQuery)
                            ->select('dispute_status_id', DB::raw('count(*) as total'))
                            ->groupBy('dispute_status_id')
                            ->pluck('total', 'dispute_status_id');

            $resolved_count = $resolvedStatusId
                ? $statusCounts->get($resolvedStatusId, 0)
                : 0;

            $dispute_statuses = DisputeStatus::latest()
                                            ->get(['id', 'dispute_status']);

            //return $disputes;
            return view('reports.staff.general-results', compact('disputes', 'filter_by', 'filter_val',
                                                            'date_raw', 'resolved_count', 'dispute_statuses',
                                                            'statusCounts', 'totalCases')
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

        $age_groups = AgeGroup::orderBy('id')
                                ->get(['id', 'age_group']);

        $case_demographics_raw = $this->fetchCaseDemographicsRaw($staff);
        $case_demographics = $this->buildCaseDemographics($type_of_cases, $age_groups, $case_demographics_raw);
        $age_group_distribution = $this->buildAgeGroupDistribution($age_groups, $case_demographics_raw);

        return view('reports.staff.case-summary', compact('total', 'date_ranges','group_by_service',
                                                    'group_by_case',
                                                    'group_by_status','dispute_statuses',
                                                    'type_of_services','type_of_cases',
                                                    'case_demographics', 'age_group_distribution'));

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

        $age_groups = AgeGroup::orderBy('id')
                                ->get(['id', 'age_group']);

        $case_demographics_raw = $this->fetchCaseDemographicsRaw($staff, $date_start, $date_end);
        $case_demographics = $this->buildCaseDemographics($type_of_cases, $age_groups, $case_demographics_raw);
        $age_group_distribution = $this->buildAgeGroupDistribution($age_groups, $case_demographics_raw);

        return view('reports.staff.case-summary', compact('total', 'date_ranges','group_by_service',
                                                    'group_by_case', 'group_by_status','dispute_statuses',
                                                    'type_of_services', 'type_of_cases',
                                                    'case_demographics', 'age_group_distribution'));
    }

    private function fetchCaseDemographicsRaw($staff, $date_start = null, $date_end = null)
    {
        $query = Dispute::query()
            ->join('beneficiaries', 'beneficiaries.id', '=', 'disputes.beneficiary_id')
            ->select(
                'disputes.type_of_case_id',
                'beneficiaries.gender',
                'beneficiaries.age_group',
                DB::raw('count(*) as total')
            )
            ->where('disputes.staff_id', $staff)
            ->groupBy('disputes.type_of_case_id', 'beneficiaries.gender', 'beneficiaries.age_group');

        if ($date_start && $date_end) {
            $query->whereBetween('disputes.reported_on', [$date_start, $date_end]);
        }

        return $query->get();
    }

    private function buildCaseDemographics($type_of_cases, $age_groups, $case_demographics_raw)
    {
        $demographics = collect($case_demographics_raw);
        $case_demographics = [];

        foreach ($type_of_cases as $type_of_case) {
            $case_items = $demographics->where('type_of_case_id', $type_of_case->id);
            $male = (int) $case_items->filter(function ($item) {
                return $this->normalizeGender($item->gender) === 'male';
            })->sum('total');
            $female = (int) $case_items->filter(function ($item) {
                return $this->normalizeGender($item->gender) === 'female';
            })->sum('total');

            $age_counts = [];
            foreach ($age_groups as $age_group) {
                $age_counts[$age_group->age_group] = (int) $case_items->where('age_group', $age_group->id)->sum('total');
            }

            $top_age_group_label = 'N/A';
            $top_age_group_count = 0;
            foreach ($age_counts as $label => $count) {
                if ($count > $top_age_group_count) {
                    $top_age_group_label = $label;
                    $top_age_group_count = $count;
                }
            }

            $case_demographics[] = [
                'label' => $type_of_case->type_of_case,
                'male' => $male,
                'female' => $female,
                'top_age_group_label' => $top_age_group_label,
                'top_age_group_count' => $top_age_group_count,
            ];
        }

        return $case_demographics;
    }

    private function buildAgeGroupDistribution($age_groups, $case_demographics_raw)
    {
        $demographics = collect($case_demographics_raw);
        $distribution = [];

        foreach ($age_groups as $age_group) {
            $age_items = $demographics->where('age_group', $age_group->id);
            $distribution[] = [
                'label' => $age_group->age_group,
                'male' => (int) $age_items->filter(function ($item) {
                    return $this->normalizeGender($item->gender) === 'male';
                })->sum('total'),
                'female' => (int) $age_items->filter(function ($item) {
                    return $this->normalizeGender($item->gender) === 'female';
                })->sum('total'),
            ];
        }

        return $distribution;
    }

    private function normalizeGender($gender)
    {
        $gender = strtolower(trim((string) $gender));
        if (in_array($gender, ['male', 'm'], true)) {
            return 'male';
        }
        if (in_array($gender, ['female', 'f'], true)) {
            return 'female';
        }
        return 'other';
    }

}
