<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Metric;
use App\Models\Dispute;
use App\Models\TypeOfCase;
use App\Models\Beneficiary;
use Illuminate\Support\Str;
use App\Services\SmsService;
use Illuminate\Http\Request;
use App\Models\DisputeStatus;
use App\Models\TypeOfService;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            return redirect()->back()
                ->withErrors('errors', 'Organization not assigned.');
        }

        $disputeBaseQuery = Dispute::query();
        $beneficiaryBaseQuery = Beneficiary::query();

        if ($organizationId) {
            $disputeBaseQuery->whereHas('reportedBy', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });

            $beneficiaryBaseQuery->whereHas('user', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            });
        }

        $total_disputes = (clone $disputeBaseQuery)->count();
        $total_beneficiaries = (clone $beneficiaryBaseQuery)->count();
        $resolvedStatusId = DisputeStatus::whereRaw('LOWER(dispute_status) = ?', ['resolved'])->value('id');
        $pendingStatusId = DisputeStatus::whereRaw('LOWER(dispute_status) = ?', ['pending'])->value('id');
        $disputes_resolved = $resolvedStatusId
            ? (clone $disputeBaseQuery)->where('dispute_status_id', $resolvedStatusId)->count()
            : 0;
        $disputes_pending = $pendingStatusId
            ? (clone $disputeBaseQuery)->where('dispute_status_id', $pendingStatusId)->count()
            : 0;
        $total_staff = $organizationId
            ? User::where('organization_id', $organizationId)
                ->whereHas('role', function ($query) {
                    $query->where('role_abbreviation', 'paralegal');
                })
                ->count()
            : Staff::count();
        $total_paralegals = User::whereHas('role', function ($query) {
            $query->where('role_abbreviation', 'paralegal');
        })->count();

        // Grouped counts
        $group_by_services = (clone $disputeBaseQuery)->select('type_of_service_id', DB::raw('COUNT(*) as total'))
            ->groupBy('type_of_service_id')
            ->get();

        $group_by_cases = (clone $disputeBaseQuery)->select('type_of_case_id', DB::raw('COUNT(*) as total'))
            ->groupBy('type_of_case_id')
            ->get();

        $group_by_statuses = (clone $disputeBaseQuery)->select('dispute_status_id', DB::raw('COUNT(*) as total'))
            ->groupBy('dispute_status_id')
            ->get();

        // Reference data
        $dispute_statuses = DisputeStatus::latest()->get(['id', 'dispute_status']);
        $type_of_services = TypeOfService::latest()->get(['id', 'type_of_service']);
        $type_of_cases = TypeOfCase::latest()->get(['id', 'type_of_case']);

        // Performance metric
        $metrics = Metric::with('metricMeasure')
            ->select('id', 'metric', 'metric_measure_id', 'metric_limit')
            ->where('metric', 'performance')
            ->firstOrFail();

        $performance = [
            'title' => $metrics->metricMeasure->metric_measure,
            'milestone' => $metrics->metric_limit,
            'value' => match ($metrics->metricMeasure->metric_measure) {
                'Disputes Registered' => $total_disputes,
                'Beneficiaries Registered' => $total_beneficiaries,
                'Disputes Resolved' => $disputes_resolved,
                default => 1000,
            }
        ];

        // Type of services data
        $tos_data = $type_of_services->map(function ($service) use ($group_by_services) {
            $match = $group_by_services->firstWhere('type_of_service_id', $service->id);
            return [
                'service' => $service->type_of_service,
                'frequency' => $match ? floor($match->total) : 0
            ];
        });

        // Type of cases data
        $toc_data = $type_of_cases->map(function ($case) use ($group_by_cases) {
            $match = $group_by_cases->firstWhere('type_of_case_id', $case->id);
            return [
                'case' => $case->type_of_case,
                'frequency' => $match ? floor($match->total) : 0
            ];
        });

        // Dispute statuses data
        $dis_data = $dispute_statuses->map(function ($status) use ($group_by_statuses) {
            $match = $group_by_statuses->firstWhere('dispute_status_id', $status->id);
            return [
                'status' => $status->dispute_status,
                'frequency' => $match ? floor($match->total) : 0
            ];
        });

        return view('dashboards.admin', compact(
            'total_disputes',
            'total_staff',
            'total_paralegals',
            'total_beneficiaries',
            'disputes_pending',
            'tos_data',
            'toc_data',
            'dis_data',
            'performance'
        ));
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function staff()
    {
        // Get the authenticated staff member's ID
        $staffId = auth()->user()->staff->id;

        // Count all disputes related to this staff
        $dispute_total = Dispute::where('staff_id', $staffId)->count();

        // Count disputes by status
        $dispute_assigned = Dispute::where('staff_id', $staffId)
            ->where('dispute_status_id', 1) // assuming 1 = assigned
            ->count();

        $dispute_proceed = Dispute::where('staff_id', $staffId)
            ->where('dispute_status_id', 2) // assuming 2 = proceeding
            ->count();

        $dispute_resolved = Dispute::where('staff_id', $staffId)
            ->where('dispute_status_id', 3) // assuming 3 = resolved
            ->count();

        // Get the latest 10 disputes assigned to this staff
        $disputes = Dispute::with(['reportedBy', 'disputeStatus', 'typeOfCase'])
            ->where('staff_id', $staffId)
            ->select([
                'id',
                'dispute_no',
                'beneficiary_id',
                'reported_on',
                'type_of_service_id',
                'type_of_case_id',
                'dispute_status_id'
            ])
            ->latest('created_at')
            ->take(10)
            ->get();

        // Return view with stats and disputes
        return view('dashboards.staff', compact(
            'disputes',
            'dispute_total',
            'dispute_assigned',
            'dispute_proceed',
            'dispute_resolved'
        ));
    }

    private function getOrganizationId()
    {
        $user = auth()->user();
        if ($user && $user->role && $user->role->role_abbreviation === 'paralegal') {
            return $user->organization_id;
        }

        return null;
    }

    private function isParalegal()
    {
        $user = auth()->user();
        return $user && $user->role && $user->role->role_abbreviation === 'paralegal';
    }
}
