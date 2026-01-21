<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Staff;
use App\Models\Income;
use App\Models\Dispute;
use App\Models\AgeGroup;
use App\Models\District;
use App\Models\TypeOfCase;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use App\Models\DisputeStatus;
use App\Models\MaritalStatus;
use App\Models\TypeOfService;
use App\Models\EducationLevel;
use App\Models\EmploymentStatus;
use App\Models\SurveyChoice;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
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
        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

        $resolvedStatusId = DisputeStatus::whereRaw('LOWER(dispute_status) = ?', ['resolved'])
                                        ->value('id');

        // Get all disputes and bind them to the index view
        $disputesQuery = Dispute::with('assignedTo:first_name,middle_name,last_name',
                                        'reportedBy:first_name,middle_name,last_name',
                                        'disputeStatus', 'typeOfService','typeOfCase')
                                ->select(['id', 'dispute_no', 'reported_on', 'beneficiary_id', 'staff_id',
                                            'type_of_service_id','type_of_case_id', 'dispute_status_id',
                                ])
                                ->latest();
        $this->scopeDisputesByOrganization($disputesQuery, $organizationId);
        $disputes = $disputesQuery->paginate(10);

        $totalCasesQuery = Dispute::query();
        $this->scopeDisputesByOrganization($totalCasesQuery, $organizationId);
        $totalCases = $totalCasesQuery->count();

        $statusCountsQuery = Dispute::select('dispute_status_id', DB::raw('count(*) as total'));
        $this->scopeDisputesByOrganization($statusCountsQuery, $organizationId);
        $statusCounts = $statusCountsQuery
                                ->groupBy('dispute_status_id')
                                ->pluck('total', 'dispute_status_id');

        $resolved_count = $resolvedStatusId
            ? $statusCounts->get($resolvedStatusId, 0)
            : 0;

        // Get all the beneficiaries and bind them to the create  view
        $beneficiariesQuery = Beneficiary::has('user')
                                    ->with('user')
                                    ->latest();
        $this->scopeBeneficiariesByOrganization($beneficiariesQuery, $organizationId);
        $beneficiaries = $beneficiariesQuery->get(['id','user_id']);

        // Get all the staff and bind them to the create  view
        $staff = Staff::has('user')
                        ->with('user')
                        ->latest()
                        ->get(['id','user_id','center_id']);

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
        return view('reports.admin.general',compact('disputes', 'beneficiaries', 'staff',
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

        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

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

                // validate individual filter value
                $this->validate($request, [
                    'all' => ['nullable', 'string', 'max:255'],
                ]);

                $search = $request->all;

                $baseQuery = Dispute::query()
                                    ->whereBetween('reported_on', [$date_start, $date_end]);

                // filter info to be displayed
                $filter_by = 'N/A';
                $filter_val = 'All';

            }elseif ($filter === 'legalAidProviderFilter') {

                // validate individual filter value
                $this->validate($request, [
                    'legal_aid_provider' => ['required', 'numeric', 'max:255'],
                ]);

                $search = $request->legal_aid_provider;

                $baseQuery = Dispute::query()
                                    ->where('staff_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end]);

                // Get full name of the search term
                $val = Staff::with('User')
                                ->select(['id', 'user_id'])
                                ->findOrFail($search);

                $val = $val->user->first_name.' '.$val->user->middle_name.' '.$val->user->last_name;

                // filter info to be displayed
                $filter_by = 'Legal Aid Providers';
                $filter_val = $val;

            }elseif ($filter === 'beneficiaryFilter') {

                // validate individual filter value
                $this->validate($request, [
                    'beneficiary' => ['required', 'numeric', 'max:255'],
                ]);

                $search = $request->beneficiary;

                $baseQuery = Dispute::query()
                                    ->where('beneficiary_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end]);

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
                                    ->whereBetween('reported_on', [$date_start, $date_end]);

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
                                    ->whereBetween('reported_on', [$date_start, $date_end]);

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
                                    ->whereBetween('reported_on', [$date_start, $date_end]);

                // Get full name of the search term
                $val = DisputeStatus::select(['id', 'dispute_status'])
                                    ->findOrFail($search);

                // filter info to be displayed
                $filter_by = 'Dispute Statuses';
                $filter_val = $val->dispute_status;

            }else {
                //return $disputes with error;
                return redirect()->back()
                                ->withErrors('errors', 'Something went wrong, please try again');
            }
        }

        if ($baseQuery) {
            $this->scopeDisputesByOrganization($baseQuery, $organizationId);

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
            return view('reports.admin.general-results', compact('disputes', 'filter_by', 'filter_val',
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
        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

        $date_ranges = 'All';

        $totalQuery = Dispute::query();
        $this->scopeDisputesByOrganization($totalQuery, $organizationId);
        $total = $totalQuery->count();

        $group_by_service = Dispute::select('type_of_service_id', Dispute::raw('count(*) as total'));
        $this->scopeDisputesByOrganization($group_by_service, $organizationId);
        $group_by_service = $group_by_service
                                    ->groupBy('type_of_service_id')
                                    ->get();

        $group_by_case = Dispute::select('type_of_case_id', Dispute::raw('count(*) as total'));
        $this->scopeDisputesByOrganization($group_by_case, $organizationId);
        $group_by_case = $group_by_case
                                ->groupBy('type_of_case_id')
                                ->get();

        $group_by_status = Dispute::select('dispute_status_id', Dispute::raw('count(*) as total'));
        $this->scopeDisputesByOrganization($group_by_status, $organizationId);
        $group_by_status = $group_by_status
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

        $case_demographics_raw = $this->fetchCaseDemographicsRaw(null, null, $organizationId);
        $case_demographics = $this->buildCaseDemographics($type_of_cases, $age_groups, $case_demographics_raw);
        $age_group_distribution = $this->buildAgeGroupDistribution($age_groups, $case_demographics_raw);

        return view('reports.admin.case-summary', compact('total', 'date_ranges','group_by_service','group_by_case',
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

        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

        // split dateRange into $date_start and $date_end

        $date_ranges = $request->dateRange;

        $date = explode(' - ', $date_ranges);

        $date_start =  Carbon::parse($date[0])->format('Y-m-d');
        $date_end = Carbon::parse($date[1])->format('Y-m-d');

        $totalQuery = Dispute::whereBetween('reported_on', [$date_start, $date_end]);
        $this->scopeDisputesByOrganization($totalQuery, $organizationId);
        $total = $totalQuery->count();

        $group_by_service = Dispute::select('type_of_service_id', Dispute::raw('count(*) as total'))
                                    ->whereBetween('reported_on', [$date_start, $date_end]);
        $this->scopeDisputesByOrganization($group_by_service, $organizationId);
        $group_by_service = $group_by_service
                                    ->groupBy('type_of_service_id')
                                    ->get();

        $group_by_case = Dispute::select('type_of_case_id', Dispute::raw('count(*) as total'))
                                    ->whereBetween('reported_on', [$date_start, $date_end]);
        $this->scopeDisputesByOrganization($group_by_case, $organizationId);
        $group_by_case = $group_by_case
                                    ->groupBy('type_of_case_id')
                                    ->get();

        $group_by_status = Dispute::select('dispute_status_id', Dispute::raw('count(*) as total'))
                                    ->whereBetween('reported_on', [$date_start, $date_end]);
        $this->scopeDisputesByOrganization($group_by_status, $organizationId);
        $group_by_status = $group_by_status
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

        $case_demographics_raw = $this->fetchCaseDemographicsRaw($date_start, $date_end, $organizationId);
        $case_demographics = $this->buildCaseDemographics($type_of_cases, $age_groups, $case_demographics_raw);
        $age_group_distribution = $this->buildAgeGroupDistribution($age_groups, $case_demographics_raw);

        return view('reports.admin.case-summary', compact('total', 'date_ranges','group_by_service','group_by_case',
                                                    'group_by_status','dispute_statuses','type_of_services',
                                                    'type_of_cases', 'case_demographics', 'age_group_distribution'));
    }

    /**
     * Get beneficiary enrollment details for summary report
     */
    public function beneficiariesEnrollSummary()
    {
        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

        $date_ranges = 'All Time';

        $totalQuery = Beneficiary::query();
        $this->scopeBeneficiariesByOrganization($totalQuery, $organizationId);
        $total = $totalQuery->count();

        $group_by_gender = Beneficiary::select('gender', Beneficiary::raw('count(*) as total'));
        $this->scopeBeneficiariesByOrganization($group_by_gender, $organizationId);
        $group_by_gender = $group_by_gender
                                        ->groupBy('gender')
                                        ->get();

        $group_by_occupation = Beneficiary::select('employment_status_id', Beneficiary::raw('count(*) as total'));
        $this->scopeBeneficiariesByOrganization($group_by_occupation, $organizationId);
        $group_by_occupation = $group_by_occupation
                                            ->groupBy('employment_status_id')
                                            ->get();

        $group_by_age = Beneficiary::select('age', Beneficiary::raw('count(*) as total'));
        $this->scopeBeneficiariesByOrganization($group_by_age, $organizationId);
        $group_by_age = $group_by_age
                                    ->groupBy('age')
                                    ->get();

        $group_by_income = Beneficiary::select('income_id', Beneficiary::raw('count(*) as total'));
        $this->scopeBeneficiariesByOrganization($group_by_income, $organizationId);
        $group_by_income = $group_by_income
                                        ->groupBy('income_id')
                                        ->get();

        $group_by_education = Beneficiary::select('education_level_id', Beneficiary::raw('count(*) as total'));
        $this->scopeBeneficiariesByOrganization($group_by_education, $organizationId);
        $group_by_education = $group_by_education
                                            ->groupBy('education_level_id')
                                            ->get();

        $group_by_marital = Beneficiary::select('marital_status_id', Beneficiary::raw('count(*) as total'));
        $this->scopeBeneficiariesByOrganization($group_by_marital, $organizationId);
        $group_by_marital = $group_by_marital
                                        ->groupBy('marital_status_id')
                                        ->get();

        // Get all the age groups
        $age_groups =AgeGroup::latest()
                                ->get(['id','age_group']);

        // Get all the income groups
        $income_groups = Income::latest()
                                ->get(['id','income']);

        // Get all the education levels
        $education_levels = EducationLevel::latest()
                                            ->get(['id','education_level']);

        // Get all the marital status
        $marital_statuses = MaritalStatus::latest()
                                            ->get(['id','marital_status']);
        // Get all the employment status
        $employment_statuses = EmploymentStatus::latest()
                                                ->get(['id','employment_status']);

        return view('reports.admin.enrollment-summary', compact('total', 'date_ranges', 'group_by_gender',
                                                            'group_by_age', 'group_by_income',
                                                            'group_by_occupation', 'group_by_education',
                                                            'group_by_marital', 'age_groups', 'income_groups',
                                                            'education_levels', 'employment_statuses',
                                                            'marital_statuses',
                                                        ));
    }


    /**
     * Filter dispute details based on date ranges
     */

    public function beneficiariesEnrollSummaryFilter(Request $request)
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

        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

        // split dateRange into $date_start and $date_end

        $date_ranges = $request->dateRange;

        $date = explode(' - ', $date_ranges);

        $date_start =  Carbon::parse($date[0])->format('Y-m-d');
        $date_end = Carbon::parse($date[1])->format('Y-m-d');

        $totalQuery = Beneficiary::whereBetween('created_at', [$date_start, $date_end]);
        $this->scopeBeneficiariesByOrganization($totalQuery, $organizationId);
        $total = $totalQuery->count();

        $group_by_gender = Beneficiary::select('gender', Beneficiary::raw('count(*) as total'))
                                        ->whereBetween('created_at', [$date_start, $date_end]);
        $this->scopeBeneficiariesByOrganization($group_by_gender, $organizationId);
        $group_by_gender = $group_by_gender
                                        ->groupBy('gender')
                                        ->get();

        $group_by_occupation = Beneficiary::select('employment_status_id', Beneficiary::raw('count(*) as total'))
                                            ->whereBetween('created_at', [$date_start, $date_end]);
        $this->scopeBeneficiariesByOrganization($group_by_occupation, $organizationId);
        $group_by_occupation = $group_by_occupation
                                            ->groupBy('employment_status_id')
                                            ->get();

        $group_by_age = Beneficiary::select('age', Beneficiary::raw('count(*) as total'))
                                    ->whereBetween('created_at', [$date_start, $date_end]);
        $this->scopeBeneficiariesByOrganization($group_by_age, $organizationId);
        $group_by_age = $group_by_age
                                    ->groupBy('age')
                                    ->get();

        $group_by_income = Beneficiary::select('income_id', Beneficiary::raw('count(*) as total'))
                                        ->whereBetween('created_at', [$date_start, $date_end]);
        $this->scopeBeneficiariesByOrganization($group_by_income, $organizationId);
        $group_by_income = $group_by_income
                                        ->groupBy('income_id')
                                        ->get();

        $group_by_education = Beneficiary::select('education_level_id', Beneficiary::raw('count(*) as total'))
                                            ->whereBetween('created_at', [$date_start, $date_end]);
        $this->scopeBeneficiariesByOrganization($group_by_education, $organizationId);
        $group_by_education = $group_by_education
                                            ->groupBy('education_level_id')
                                            ->get();

        $group_by_marital = Beneficiary::select('marital_status_id', Beneficiary::raw('count(*) as total'))
                                        ->whereBetween('created_at', [$date_start, $date_end]);
        $this->scopeBeneficiariesByOrganization($group_by_marital, $organizationId);
        $group_by_marital = $group_by_marital
                                        ->groupBy('marital_status_id')
                                        ->get();

        // Get all the age groups
        $age_groups =AgeGroup::latest()
                                ->get(['id','age_group']);

        // Get all the income groups
        $income_groups = Income::latest()
                                    ->get(['id','income']);

        // Get all the education levels
        $education_levels = EducationLevel::latest()
                                            ->get(['id','education_level']);

        // Get all the marital status
        $marital_statuses = MaritalStatus::latest()
                                            ->get(['id','marital_status']);
        // Get all the employment status
        $employment_statuses = EmploymentStatus::latest()
                                                    ->get(['id','employment_status']);

        return view('reports.admin.enrollment-summary', compact('total', 'date_ranges', 'group_by_gender',
                                                    'group_by_age', 'group_by_income',
                                                    'group_by_occupation', 'group_by_education',
                                                    'group_by_marital', 'age_groups', 'income_groups',
                                                    'education_levels', 'employment_statuses',
                                                    'marital_statuses',
                                                ));
    }


    /**
     * Get survey choices frequency for summary report
     */
    public function surveySummary(){

        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

        $totalQuery = Beneficiary::query();
        $this->scopeBeneficiariesByOrganization($totalQuery, $organizationId);
        $total = $totalQuery->count();

        $group_by_survey = Beneficiary::select('survey_choice_id', Beneficiary::raw('count(*) as total'));
        $this->scopeBeneficiariesByOrganization($group_by_survey, $organizationId);
        $group_by_survey = $group_by_survey
                                        ->groupBy('survey_choice_id')
                                        ->get();

        // Get all the age groups
        $survey_choices =SurveyChoice::latest()
                                        ->get(['id','survey_choice', 'choice_abbr']);

        // create chart data and store in array;
        $data_arr = [];

        if ($survey_choices->count())
        {
            foreach ($survey_choices as $survey_choice)
            {
                if ($group_by_survey->count())
                {
                    foreach ($group_by_survey as $survey)
                    {
                        if ($survey_choice->id === $survey->survey_choice_id)
                        {
                            $gbm = floor($survey->total) ?? 0;
                            break;
                        }
                        else {
                            $gbm = 0;
                        }
                    }
                    $gb = ['choice' => $survey_choice->choice_abbr, 'frequency' => $gbm];
                }
                else{

                    $gb = ['choice' => $survey_choice->choice_abbr, 'frequency' => 0];
                }

                $data_arr[] = $gb;
            }
        }

        return view('reports.admin.survey-summary', compact('total', 'group_by_survey', 'survey_choices', 'data_arr'));
    }

    /**
     * Filter dispute details based on date ranges
     */

    public function surveySummaryFilter(Request $request)
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

        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

        // split dateRange into $date_start and $date_end

        $date_ranges = $request->dateRange;

        $date = explode(' - ', $date_ranges);

        $date_start =  Carbon::parse($date[0])->format('Y-m-d');
        $date_end = Carbon::parse($date[1])->format('Y-m-d');

        $totalQuery = Beneficiary::whereBetween('created_at', [$date_start, $date_end]);
        $this->scopeBeneficiariesByOrganization($totalQuery, $organizationId);
        $total = $totalQuery->count();

        $group_by_survey = Beneficiary::select('survey_choice_id', Beneficiary::raw('count(*) as total'))
                                        ->whereBetween('created_at', [$date_start, $date_end]);
        $this->scopeBeneficiariesByOrganization($group_by_survey, $organizationId);
        $group_by_survey = $group_by_survey
                                        ->groupBy('survey_choice_id')
                                        ->get();

        // Get all the age groups
        $survey_choices =SurveyChoice::latest()
                                        ->get(['id','survey_choice', 'choice_abbr']);

        // create chart data and store in array;
        $data_arr = [];

        if ($survey_choices->count())
        {
            foreach ($survey_choices as $survey_choice)
            {
                if ($group_by_survey->count())
                {
                    foreach ($group_by_survey as $survey)
                    {
                        if ($survey_choice->id === $survey->survey_choice_id)
                        {
                            $gbm = floor($survey->total) ?? 0;
                            break;
                        }
                        else {
                            $gbm = 0;
                        }
                    }
                    $gb = ['choice' => $survey_choice->choice_abbr, 'frequency' => $gbm];
                }
                else{

                    $gb = ['choice' => $survey_choice->choice_abbr, 'frequency' => 0];
                }

                $data_arr[] = $gb;
            }
        }

        return view('reports.admin.survey-summary', compact('total', 'group_by_survey', 'survey_choices', 'data_arr'))
                ->with('status', 'Survey data found');
    }

    /**
     * Paralegal reports summary.
     */
    public function paralegalSummary()
    {
        $user = auth()->user();
        if (!$user || !$user->role || !in_array($user->role->role_abbreviation, ['superadmin', 'admin'], true)) {
            return redirect()->back()
                ->withErrors('errors', 'You are not authorized to perform this action.');
        }

        $baseQuery = Dispute::query()
            ->join('beneficiaries', 'beneficiaries.id', '=', 'disputes.beneficiary_id')
            ->where('beneficiaries.registration_source', 'paralegal');

        $total_cases = (clone $baseQuery)->count();

        $case_type_counts = (clone $baseQuery)
            ->whereNotNull('disputes.type_of_case_id')
            ->select('disputes.type_of_case_id', DB::raw('count(*) as total'))
            ->groupBy('disputes.type_of_case_id')
            ->pluck('total', 'disputes.type_of_case_id');

        $service_counts = (clone $baseQuery)
            ->whereNotNull('disputes.type_of_service_id')
            ->select('disputes.type_of_service_id', DB::raw('count(*) as total'))
            ->groupBy('disputes.type_of_service_id')
            ->pluck('total', 'disputes.type_of_service_id');

        $gender_counts = (clone $baseQuery)
            ->whereNotNull('beneficiaries.gender')
            ->select('beneficiaries.gender', DB::raw('count(*) as total'))
            ->groupBy('beneficiaries.gender')
            ->pluck('total', 'beneficiaries.gender');

        $district_counts = (clone $baseQuery)
            ->whereNotNull('beneficiaries.district_id')
            ->select('beneficiaries.district_id', DB::raw('count(*) as total'))
            ->groupBy('beneficiaries.district_id')
            ->pluck('total', 'beneficiaries.district_id');

        $ward_counts = (clone $baseQuery)
            ->whereNotNull('beneficiaries.ward')
            ->where('beneficiaries.ward', '!=', '')
            ->select('beneficiaries.ward', DB::raw('count(*) as total'))
            ->groupBy('beneficiaries.ward')
            ->orderBy('beneficiaries.ward')
            ->get();

        $case_types = TypeOfCase::orderBy('type_of_case')->get(['id', 'type_of_case']);
        $service_types = TypeOfService::orderBy('type_of_service')->get(['id', 'type_of_service']);

        $districts = $district_counts->isEmpty()
            ? collect()
            : District::whereIn('id', $district_counts->keys())
                ->orderBy('district')
                ->get(['id', 'district']);

        return view('reports.admin.paralegal-summary', compact(
            'total_cases',
            'case_types',
            'service_types',
            'districts',
            'case_type_counts',
            'service_counts',
            'gender_counts',
            'district_counts',
            'ward_counts'
        ));
    }

    private function fetchCaseDemographicsRaw($date_start = null, $date_end = null, $organizationId = null)
    {
        $query = Dispute::query()
            ->join('beneficiaries', 'beneficiaries.id', '=', 'disputes.beneficiary_id')
            ->join('users', 'users.id', '=', 'beneficiaries.user_id')
            ->select(
                'disputes.type_of_case_id',
                'beneficiaries.gender',
                'beneficiaries.age_group',
                DB::raw('count(*) as total')
            )
            ->groupBy('disputes.type_of_case_id', 'beneficiaries.gender', 'beneficiaries.age_group');

        if ($organizationId) {
            $query->where('users.organization_id', $organizationId);
        }

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

    private function isParalegal()
    {
        $user = auth()->user();
        return $user && $user->role && $user->role->role_abbreviation === 'paralegal';
    }

    private function getOrganizationId()
    {
        return $this->isParalegal() ? auth()->user()->organization_id : null;
    }

    private function scopeDisputesByOrganization($query, $organizationId)
    {
        if (!$organizationId) {
            return $query;
        }

        return $query->whereHas('reportedBy', function ($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        });
    }

    private function scopeBeneficiariesByOrganization($query, $organizationId)
    {
        if (!$organizationId) {
            return $query;
        }

        return $query->whereHas('user', function ($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        });
    }

}
