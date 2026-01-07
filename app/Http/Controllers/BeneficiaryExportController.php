<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Beneficiary;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class BeneficiaryExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function exportListPdf(Request $request)
    {
        $this->preparePdfRuntime();
        $beneficiaries = $this->buildListQuery($request)->get();

        $pdf = PDF::loadView('exports.beneficiaries-list', [
            'beneficiaries' => $beneficiaries,
            'search' => $request->get('search'),
            'generatedAt' => Carbon::now()->format('d-m-Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('beneficiaries_' . time() . '.pdf');
    }

    public function exportListExcel(Request $request)
    {
        $beneficiaries = $this->buildListQuery($request)->get();
        $rows = $this->buildListRows($beneficiaries);
        $headings = [[
            'S/N', 'File No', 'Full Name', 'Telephone', 'District', 'Region', 'Enrolled On', 'Status'
        ]];

        return Excel::download(new GenericExport($rows, $headings), 'beneficiaries_' . time() . '.xlsx');
    }

    public function exportListCsv(Request $request)
    {
        $beneficiaries = $this->buildListQuery($request)->get();
        $rows = $this->buildListRows($beneficiaries);
        $headings = [[
            'S/N', 'File No', 'Full Name', 'Telephone', 'District', 'Region', 'Enrolled On', 'Status'
        ]];

        return Excel::download(new GenericExport($rows, $headings), 'beneficiaries_' . time() . '.csv');
    }

    public function exportProfilePdf($locale, Beneficiary $beneficiary)
    {
        $this->preparePdfRuntime();
        $beneficiary->load([
            'user',
            'district.region',
            'region',
            'educationLevel',
            'maritalStatus',
            'employmentStatus',
        ]);

        $pdf = PDF::loadView('exports.beneficiary-profile', [
            'beneficiary' => $beneficiary,
            'rows' => $this->buildProfileRows($beneficiary),
            'generatedAt' => Carbon::now()->format('d-m-Y H:i'),
        ]);

        return $pdf->download('beneficiary_' . $beneficiary->id . '_' . time() . '.pdf');
    }

    public function exportProfileExcel($locale, Beneficiary $beneficiary)
    {
        $rows = $this->buildProfileRows($beneficiary);
        $headings = [['Field', 'Value']];

        return Excel::download(new GenericExport($rows, $headings), 'beneficiary_' . $beneficiary->id . '_' . time() . '.xlsx');
    }

    public function exportProfileCsv($locale, Beneficiary $beneficiary)
    {
        $rows = $this->buildProfileRows($beneficiary);
        $headings = [['Field', 'Value']];

        return Excel::download(new GenericExport($rows, $headings), 'beneficiary_' . $beneficiary->id . '_' . time() . '.csv');
    }

    private function buildListQuery(Request $request)
    {
        $query = Beneficiary::whereHas('user')
            ->with(['user', 'district.region'])
            ->latest();

        if ($search = $request->get('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function buildListRows($beneficiaries)
    {
        $rows = [];
        foreach ($beneficiaries as $index => $beneficiary) {
            $fullName = trim(implode(' ', array_filter([
                $beneficiary->user->first_name ?? '',
                $beneficiary->user->middle_name ?? '',
                $beneficiary->user->last_name ?? '',
            ])));

            $rows[] = [
                $index + 1,
                $beneficiary->user->user_no ?? '',
                $fullName,
                $beneficiary->user->tel_no ?? '',
                optional($beneficiary->district)->district ?? '',
                optional(optional($beneficiary->district)->region)->region ?? '',
                $beneficiary->created_at ? Carbon::parse($beneficiary->created_at)->format('d-m-Y') : '',
                ($beneficiary->user && $beneficiary->user->is_active) ? 'Active' : 'Inactive',
            ];
        }

        return $rows;
    }

    private function buildProfileRows(Beneficiary $beneficiary)
    {
        $fullName = trim(implode(' ', array_filter([
            $beneficiary->user->first_name ?? '',
            $beneficiary->user->middle_name ?? '',
            $beneficiary->user->last_name ?? '',
        ])));

        return [
            ['File No', $beneficiary->user->user_no ?? ''],
            ['Username', $beneficiary->user->name ?? ''],
            ['Full Name', $fullName],
            ['Gender', $beneficiary->gender ?? ''],
            ['Age', $beneficiary->age ?? ''],
            ['Disabled', $beneficiary->disabled ?? ''],
            ['Education Level', optional($beneficiary->educationLevel)->education_level ?? ''],
            ['Telephone No', $beneficiary->user->tel_no ?? ''],
            ['Telephone No 2', $beneficiary->user->mobile_no ?? ''],
            ['Status', ($beneficiary->user && $beneficiary->user->is_active) ? 'Active' : 'Inactive'],
            ['Region', optional($beneficiary->region)->region ?? ''],
            ['District', optional($beneficiary->district)->district ?? ''],
            ['Ward', $beneficiary->ward ?? ''],
            ['Street', $beneficiary->street ?? ''],
            ['Address', $beneficiary->address ?? ''],
            ['Marital Status', optional($beneficiary->maritalStatus)->marital_status ?? ''],
            ['Number of Children', $beneficiary->no_of_children ?? ''],
            ['Financial Capability', $beneficiary->financial_capability ?? ''],
            ['Employment Status', optional($beneficiary->employmentStatus)->employment_status ?? ''],
            ['Occupation / Business', $beneficiary->occupation_business ?? ''],
            ['Enrolled On', $beneficiary->created_at ? Carbon::parse($beneficiary->created_at)->format('d-m-Y') : ''],
        ];
    }

    private function preparePdfRuntime()
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
    }
}
