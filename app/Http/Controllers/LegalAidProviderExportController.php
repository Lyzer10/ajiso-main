<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class LegalAidProviderExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function exportListPdf(Request $request)
    {
        $staff = $this->buildListQuery($request)->get();

        $pdf = PDF::loadView('exports.staff-list', [
            'staff' => $staff,
            'search' => $request->get('search'),
            'generatedAt' => Carbon::now()->format('d-m-Y H:i'),
        ]);

        return $pdf->download('legal_aid_providers_' . time() . '.pdf');
    }

    public function exportListExcel(Request $request)
    {
        $staff = $this->buildListQuery($request)->get();
        $rows = $this->buildListRows($staff);
        $headings = [[
            'S/N', 'Username', 'Full Name', 'Email', 'Office', 'Status'
        ]];

        return Excel::download(new GenericExport($rows, $headings), 'legal_aid_providers_' . time() . '.xlsx');
    }

    public function exportListCsv(Request $request)
    {
        $staff = $this->buildListQuery($request)->get();
        $rows = $this->buildListRows($staff);
        $headings = [[
            'S/N', 'Username', 'Full Name', 'Email', 'Office', 'Status'
        ]];

        return Excel::download(new GenericExport($rows, $headings), 'legal_aid_providers_' . time() . '.csv');
    }

    public function exportProfilePdf($locale, Staff $staff)
    {
        $staff->load(['user', 'center']);

        $pdf = PDF::loadView('exports.staff-profile', [
            'staff' => $staff,
            'rows' => $this->buildProfileRows($staff),
            'generatedAt' => Carbon::now()->format('d-m-Y H:i'),
        ]);

        return $pdf->download('legal_aid_provider_' . $staff->id . '_' . time() . '.pdf');
    }

    public function exportProfileExcel($locale, Staff $staff)
    {
        $rows = $this->buildProfileRows($staff);
        $headings = [['Field', 'Value']];

        return Excel::download(new GenericExport($rows, $headings), 'legal_aid_provider_' . $staff->id . '_' . time() . '.xlsx');
    }

    public function exportProfileCsv($locale, Staff $staff)
    {
        $rows = $this->buildProfileRows($staff);
        $headings = [['Field', 'Value']];

        return Excel::download(new GenericExport($rows, $headings), 'legal_aid_provider_' . $staff->id . '_' . time() . '.csv');
    }

    private function buildListQuery(Request $request)
    {
        $query = Staff::has('user')
            ->with(['user', 'center'])
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

    private function buildListRows($staff)
    {
        $rows = [];
        foreach ($staff as $index => $member) {
            $fullName = trim(implode(' ', array_filter([
                $member->user->first_name ?? '',
                $member->user->middle_name ?? '',
                $member->user->last_name ?? '',
            ])));

            $rows[] = [
                $index + 1,
                $member->user->name ?? '',
                $fullName,
                $member->user->email ?? '',
                optional($member->center)->location ?? '',
                ($member->is_assigned == 1) ? 'assigned' : 'unassigned',
            ];
        }

        return $rows;
    }

    private function buildProfileRows(Staff $staff)
    {
        $fullName = trim(implode(' ', array_filter([
            $staff->user->first_name ?? '',
            $staff->user->middle_name ?? '',
            $staff->user->last_name ?? '',
        ])));

        return [
            ['Username', $staff->user->name ?? ''],
            ['Full Name', $fullName],
            ['Designation', optional($staff->user->designation)->name ?? ''],
            ['Email', $staff->user->email ?? ''],
            ['Telephone', $staff->user->tel_no ?? ''],
            ['Office', optional($staff->center)->location ?? ''],
            ['Assignment', ($staff->is_assigned == 1) ? 'assigned' : 'unassigned'],
        ];
    }
}
