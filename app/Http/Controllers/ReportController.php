<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Staff;
use App\Models\Income;
use App\Models\Dispute;
use App\Models\AgeGroup;
use App\Models\TypeOfCase;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use App\Models\DisputeStatus;
use App\Models\MaritalStatus;
use App\Models\TypeOfService;
use App\Models\EducationLevel;
use App\Models\EmploymentStatus;
use App\Models\SurveyChoice;

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
        // Get all disputes and bind them to the index view
        $disputes = Dispute::with('assignedTo:first_name,middle_name,last_name',
                                    'reportedBy:first_name,middle_name,last_name',
                                    'disputeStatus', 'typeOfService','typeOfCase')
                            ->select(['id', 'dispute_no', 'reported_on', 'beneficiary_id', 'staff_id',
                                        'type_of_service_id','type_of_case_id', 'dispute_status_id',
                            ])
                            ->latest()
                            ->paginate(10);

        // Get count of resolved disputes
        $resolved_count = Dispute::where('dispute_status_id', '3')
                                    ->count();

        // Get all the beneficiaries and bind them to the create  view
        $beneficiaries = Beneficiary::has('user')
                                    ->with('user')
                                    ->latest()
                                    ->get(['id','user_id']);

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

        $disputes = [];

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

                $disputes = Dispute::with('assignedTo', 'reportedBy', 'disputeStatus', 'typeOfCase')
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->latest()
                                    ->select(['id', 'dispute_no', 'beneficiary_id', 'staff_id', 'reported_on',
                                            'type_of_service_id','type_of_case_id', 'dispute_status_id'])
                                    ->paginate(10);

                // filter info to be displayed
                $filter_by = 'N/A';
                $filter_val = 'All';

            }elseif ($filter === 'legalAidProviderFilter') {

                // validate individual filter value
                $this->validate($request, [
                    'legal_aid_provider' => ['required', 'numeric', 'max:255'],
                ]);

                $search = $request->legal_aid_provider;

                $disputes = Dispute::with('assignedTo', 'reportedBy', 'disputeStatus', 'typeOfCase')
                                    ->where('staff_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->latest()
                                    ->select(['id', 'dispute_no', 'beneficiary_id', 'staff_id', 'reported_on',
                                                'type_of_service_id', 'type_of_case_id', 'dispute_status_id'])
                                    ->paginate(10);

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

                $disputes = Dispute::with('assignedTo', 'reportedBy', 'disputeStatus', 'typeOfCase')
                                    ->where('beneficiary_id', '=', $search)
                                    ->whereBetween('reported_on', [$date_start, $date_end])
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
                //return $disputes with error;
                return redirect()->back()
                                ->withErrors('errors', 'Something went wrong, please try again');
            }
        }

        if ($disputes) {

            // Get count of resolved disputes
            $resolved_count = $disputes->where('dispute_status_id', '3')
                                        ->count();

            //return $disputes;
            return view('reports.admin.general-results', compact('disputes', 'filter_by', 'filter_val',
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
        $date_ranges = 'All';

        $total = Dispute::count();

        $group_by_service = Dispute::select('type_of_service_id', Dispute::raw('count(*) as total'))
                                    ->groupBy('type_of_service_id')
                                    ->get();

        $group_by_case = Dispute::select('type_of_case_id', Dispute::raw('count(*) as total'))
                                ->groupBy('type_of_case_id')
                                ->get();

        $group_by_status = Dispute::select('dispute_status_id', Dispute::raw('count(*) as total'))
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

        return view('reports.admin.case-summary', compact('total', 'date_ranges','group_by_service','group_by_case',
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

        // split dateRange into $date_start and $date_end

        $date_ranges = $request->dateRange;

        $date = explode(' - ', $date_ranges);

        $date_start =  Carbon::parse($date[0])->format('Y-m-d');
        $date_end = Carbon::parse($date[1])->format('Y-m-d');

        $total = Dispute::whereBetween('reported_on', [$date_start, $date_end])
                        ->count();

        $group_by_service = Dispute::select('type_of_service_id', Dispute::raw('count(*) as total'))
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->groupBy('type_of_service_id')
                                    ->get();

        $group_by_case = Dispute::select('type_of_case_id', Dispute::raw('count(*) as total'))
                                    ->whereBetween('reported_on', [$date_start, $date_end])
                                    ->groupBy('type_of_case_id')
                                    ->get();

        $group_by_status = Dispute::select('dispute_status_id', Dispute::raw('count(*) as total'))
                                    ->whereBetween('reported_on', [$date_start, $date_end])
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

        return view('reports.admin.case-summary', compact('total', 'date_ranges','group_by_service','group_by_case',
                                                    'group_by_status','dispute_statuses','type_of_services',
                                                    'type_of_cases'));
    }

    /**
     * Get beneficiary enrollment details for summary report
     */
    public function beneficiariesEnrollSummary()
    {

        $date_ranges = 'All Time';

        $total = Beneficiary::count();

        $group_by_gender = Beneficiary::select('gender', Beneficiary::raw('count(*) as total'))
                                        ->groupBy('gender')
                                        ->get();

        $group_by_occupation = Beneficiary::select('employment_status_id', Beneficiary::raw('count(*) as total'))
                                            ->groupBy('employment_status_id')
                                            ->get();

        $group_by_age = Beneficiary::select('age', Beneficiary::raw('count(*) as total'))
                                    ->groupBy('age')
                                    ->get();

        $group_by_income = Beneficiary::select('income_id', Beneficiary::raw('count(*) as total'))
                                        ->groupBy('income_id')
                                        ->get();

        $group_by_education = Beneficiary::select('education_level_id', Beneficiary::raw('count(*) as total'))
                                            ->groupBy('education_level_id')
                                            ->get();

        $group_by_marital = Beneficiary::select('marital_status_id', Beneficiary::raw('count(*) as total'))
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

        // split dateRange into $date_start and $date_end

        $date_ranges = $request->dateRange;

        $date = explode(' - ', $date_ranges);

        $date_start =  Carbon::parse($date[0])->format('Y-m-d');
        $date_end = Carbon::parse($date[1])->format('Y-m-d');

        $total = Beneficiary::whereBetween('created_at', [$date_start, $date_end])
                            ->count();

        $group_by_gender = Beneficiary::select('gender', Beneficiary::raw('count(*) as total'))
                                        ->whereBetween('created_at', [$date_start, $date_end])
                                        ->groupBy('gender')
                                        ->get();

        $group_by_occupation = Beneficiary::select('employment_status_id', Beneficiary::raw('count(*) as total'))
                                            ->whereBetween('created_at', [$date_start, $date_end])
                                            ->groupBy('employment_status_id')
                                            ->get();

        $group_by_age = Beneficiary::select('age', Beneficiary::raw('count(*) as total'))
                                    ->whereBetween('created_at', [$date_start, $date_end])
                                    ->groupBy('age')
                                    ->get();

        $group_by_income = Beneficiary::select('income_id', Beneficiary::raw('count(*) as total'))
                                        ->whereBetween('created_at', [$date_start, $date_end])
                                        ->groupBy('income_id')
                                        ->get();

        $group_by_education = Beneficiary::select('education_level_id', Beneficiary::raw('count(*) as total'))
                                            ->whereBetween('created_at', [$date_start, $date_end])
                                            ->groupBy('education_level_id')
                                            ->get();

        $group_by_marital = Beneficiary::select('marital_status_id', Beneficiary::raw('count(*) as total'))
                                        ->whereBetween('created_at', [$date_start, $date_end])
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

        $total = Beneficiary::count();

        $group_by_survey = Beneficiary::select('survey_choice_id', Beneficiary::raw('count(*) as total'))
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

        // split dateRange into $date_start and $date_end

        $date_ranges = $request->dateRange;

        $date = explode(' - ', $date_ranges);

        $date_start =  Carbon::parse($date[0])->format('Y-m-d');
        $date_end = Carbon::parse($date[1])->format('Y-m-d');

        $total = Beneficiary::whereBetween('created_at', [$date_start, $date_end])
                                ->count();

        $group_by_survey = Beneficiary::select('survey_choice_id', Beneficiary::raw('count(*) as total'))
                                        ->whereBetween('created_at', [$date_start, $date_end])
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

}
