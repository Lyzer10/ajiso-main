<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserRole;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ParalegalExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function exportListPdf(Request $request)
    {
        $this->preparePdfRuntime();
        $users = $this->buildListQuery($request)->get();

        $organizationName = null;
        if ($organizationId = $request->get('organization_id')) {
            $organizationName = Organization::where('id', $organizationId)->value('name');
        }

        $pdf = PDF::loadView('exports.paralegals-list', [
            'users' => $users,
            'search' => $request->get('search'),
            'organizationName' => $organizationName,
            'generatedAt' => Carbon::now()->format('d-m-Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('paralegals_' . time() . '.pdf');
    }

    public function exportListExcel(Request $request)
    {
        $users = $this->buildListQuery($request)->get();
        $rows = $this->buildListRows($users);
        $headings = [[
            'S/N', 'Username', 'Full Name', 'Email', 'Organization', 'Status'
        ]];

        return Excel::download(new GenericExport($rows, $headings), 'paralegals_' . time() . '.xlsx');
    }

    public function exportListCsv(Request $request)
    {
        $users = $this->buildListQuery($request)->get();
        $rows = $this->buildListRows($users);
        $headings = [[
            'S/N', 'Username', 'Full Name', 'Email', 'Organization', 'Status'
        ]];

        return Excel::download(new GenericExport($rows, $headings), 'paralegals_' . time() . '.csv');
    }

    private function buildListQuery(Request $request)
    {
        $paralegalRoleId = UserRole::where('role_abbreviation', 'paralegal')->value('id');

        $query = User::with(['role:id,role_abbreviation,role_name', 'organization:id,name'])
            ->select([
                'id',
                'name',
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'is_active',
                'user_role_id',
                'organization_id',
            ])
            ->latest();

        if ($paralegalRoleId) {
            $query->where('user_role_id', $paralegalRoleId);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($subQuery) use ($search) {
                $like = '%' . $search . '%';
                $subQuery->where('name', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('middle_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        if ($organizationId = $request->get('organization_id')) {
            $query->where('organization_id', $organizationId);
        }

        return $query;
    }

    private function buildListRows($users)
    {
        $rows = [];
        foreach ($users as $index => $user) {
            $fullName = trim(implode(' ', array_filter([
                $user->first_name ?? '',
                $user->middle_name ?? '',
                $user->last_name ?? '',
            ])));

            $rows[] = [
                $index + 1,
                $user->name ?? '',
                $fullName,
                $user->email ?? '',
                optional($user->organization)->name ?? '',
                ((bool) $user->is_active === true) ? 'Active' : 'Inactive',
            ];
        }

        return $rows;
    }

    private function preparePdfRuntime()
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
    }
}
