<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Dispute;
use App\Models\TypeOfCase;
use App\Models\DisputeStatus;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class DisputeExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function exportListPdf(Request $request)
    {
        $this->preparePdfRuntime();
        $disputes = $this->buildListQuery($request)->get();

        $pdf = PDF::loadView('exports.disputes-list', [
            'disputes' => $disputes,
            'filters' => $this->buildFilterSummary($request),
            'generatedAt' => Carbon::now()->format('d-m-Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('disputes_' . time() . '.pdf');
    }

    public function exportListExcel(Request $request)
    {
        $disputes = $this->buildListQuery($request)->get();
        $rows = $this->buildListRows($disputes);
        $headings = [[
            'S/N', 'Dispute No', 'Case Type', 'Beneficiary', 'Legal Aid Provider', 'Reported', 'Status'
        ]];

        return Excel::download(new GenericExport($rows, $headings), 'disputes_' . time() . '.xlsx');
    }

    public function exportListCsv(Request $request)
    {
        $disputes = $this->buildListQuery($request)->get();
        $rows = $this->buildListRows($disputes);
        $headings = [[
            'S/N', 'Dispute No', 'Case Type', 'Beneficiary', 'Legal Aid Provider', 'Reported', 'Status'
        ]];

        return Excel::download(new GenericExport($rows, $headings), 'disputes_' . time() . '.csv');
    }

    private function buildListQuery(Request $request)
    {
        $query = Dispute::has('reportedBy')
            ->with('assignedTo', 'reportedBy', 'disputeStatus', 'typeOfCase')
            ->select([
                'id',
                'dispute_no',
                'beneficiary_id',
                'staff_id',
                'reported_on',
                'type_of_case_id',
                'dispute_status_id',
            ])
            ->latest();

        $this->scopeDisputesByOrganization($query);

        if ($statusId = $request->get('status')) {
            $query->where('dispute_status_id', (int) $statusId);
        }

        if ($caseTypeId = $request->get('case_type')) {
            $query->where('type_of_case_id', (int) $caseTypeId);
        }

        [$periodStart, $periodEnd] = $this->resolvePeriodRange($request->get('period'), $request->get('dateRange'));
        if ($periodStart && $periodEnd) {
            $query->whereBetween('reported_on', [$periodStart, $periodEnd]);
        }

        if ($search = $request->get('search')) {
            $query->whereHas('reportedBy', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function buildListRows($disputes)
    {
        $rows = [];
        foreach ($disputes as $index => $dispute) {
            $beneficiary = trim(implode(' ', array_filter([
                optional($dispute->reportedBy)->first_name,
                optional($dispute->reportedBy)->middle_name,
                optional($dispute->reportedBy)->last_name,
            ])));

            $staff = $dispute->staff_id
                ? trim(implode(' ', array_filter([
                    optional($dispute->assignedTo)->first_name,
                    optional($dispute->assignedTo)->middle_name,
                    optional($dispute->assignedTo)->last_name,
                ])))
                : 'Unassigned';

            $rows[] = [
                $index + 1,
                $dispute->dispute_no,
                optional($dispute->typeOfCase)->type_of_case ?? '',
                $beneficiary,
                $staff,
                $dispute->reported_on ? Carbon::parse($dispute->reported_on)->format('d-m-Y') : '',
                optional($dispute->disputeStatus)->dispute_status ?? '',
            ];
        }

        return $rows;
    }

    private function buildFilterSummary(Request $request)
    {
        $summary = [];

        if ($search = $request->get('search')) {
            $summary[] = 'Search: ' . $search;
        }

        if ($caseTypeId = $request->get('case_type')) {
            $caseType = TypeOfCase::find($caseTypeId);
            if ($caseType) {
                $summary[] = 'Case Type: ' . $caseType->type_of_case;
            }
        }

        if ($statusId = $request->get('status')) {
            $status = DisputeStatus::find($statusId);
            if ($status) {
                $summary[] = 'Status: ' . $status->dispute_status;
            }
        }

        if ($period = $request->get('period')) {
            $summary[] = 'Period: ' . Str::title(str_replace('_', ' ', $period));
        }

        if ($dateRange = $request->get('dateRange')) {
            $summary[] = 'Date Range: ' . $dateRange;
        }

        return implode(' | ', $summary);
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

    private function preparePdfRuntime()
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
    }

    private function isParalegal()
    {
        $user = auth()->user();
        return $user && $user->role && in_array($user->role->role_abbreviation, ['paralegal', 'clerk'], true);
    }

    private function getOrganizationId()
    {
        return $this->isParalegal() ? auth()->user()->organization_id : null;
    }

    private function scopeDisputesByOrganization($query)
    {
        $organizationId = $this->getOrganizationId();
        if ($this->isParalegal() && !$organizationId) {
            abort(403, 'Organization not assigned.');
        }
        if (!$organizationId) {
            return;
        }

        $query->whereHas('reportedBy', function ($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        });
    }
}
