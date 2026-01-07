<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Dispute;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\DisputesExport;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
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
     * @return collection
     */
    public function fetchDisputes($input)
    {
        $disputes = Dispute::with('assignedTo:first_name,middle_name,last_name',
                                    'reportedBy:first_name,middle_name,last_name',
                                    'disputeStatus', 'typeOfService','typeOfCase')
                            ->select(['id', 'dispute_no', 'reported_on', 'beneficiary_id', 'staff_id',
                                        'type_of_service_id','type_of_case_id', 'dispute_status_id',
                            ])
                            ->whereIn('id', $input)
                            ->latest()
                            ->get();

        return $disputes;
    }

    public function collectDisputes($disputes_list)
    {
        foreach ($disputes_list as $disputes) {

            $collection[] = collect(
                                    [
                                        '#'.$disputes->id,
                                        $disputes->dispute_no,
                                        Carbon::parse($disputes->reported_on)->format('d-m-Y') ,
                                        $disputes->reportedBy->first_name.' '.$disputes->reportedBy->middle_name.' '.$disputes->reportedBy->last_name,
                                        (is_null($disputes->staff_id)) ? 'Unassigned' : $disputes->assignedTo->first_name.' '.$disputes->assignedTo->middle_name.' '.$disputes->assignedTo->last_name,
                                        $disputes->typeOfService->type_of_service,
                                        $disputes->typeOfCase->type_of_case ,
                                        $disputes->disputeStatus->dispute_status,
                                    ]
                                );
        }

        return $collection;
    }

    public function exportPdf(Request $request)
    {
        $this->preparePdfRuntime();
        $disputes_input = $request->dispute;
        $filter_by = $request->filter_by;
        $filter_val = $request->filter_val ;
        $date_raw = $request->date_raw;
        $resolved_count = $request->resolved_count;

        $disputes = $this->fetchDisputes($disputes_input);

        $pdf = PDF::loadView('reports.admin.export-preview', [
                                                        'disputes' => $disputes,
                                                        'filter_by' => $filter_by,
                                                        'filter_val' => $filter_val,
                                                        'date_raw' => $date_raw,
                                                        'resolved_count' => $resolved_count
                                                        ]
                                                    );

        return $pdf->setPaper('a4', 'landscape')
                    ->download('report_'.time());

        return redirect()->back()
                        ->withSuccess('Export started!');

    }

    public function exportExcel(Request $request)
    {
        $disputes_input = $request->dispute;
        $filter_by = $request->filter_by;
        $filter_val = $request->filter_val ;
        $date_raw = $request->date_raw;
        $resolved_count = $request->resolved_count;
        $disputes_count = count($disputes_input);

        $disputes_list = $this->fetchDisputes($disputes_input);

        $title = [
                    'filter_by' => $filter_by,
                    'filter_val' => $filter_val,
                    'date_raw' => $date_raw,
                    'resolved_count' => $resolved_count,
                    'disputes_count' => $disputes_count
                ];

        $collection = $this->collectDisputes($disputes_list);

        try {
            // Download the excel file
            return Excel::download(new DisputesExport($collection, $title), 'report_'.time().'.xlsx');

            return back()->withSuccess('Export started!');

        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    public function exportCsv(Request $request)
    {
        $disputes_input = $request->dispute;
        $filter_by = $request->filter_by;
        $filter_val = $request->filter_val ;
        $date_raw = $request->date_raw;
        $resolved_count = $request->resolved_count;
        $disputes_count = count($disputes_input);

        $disputes_list = $this->fetchDisputes($disputes_input);

        $title = [
                    'filter_by' => $filter_by,
                    'filter_val' => $filter_val,
                    'date_raw' => $date_raw,
                    'resolved_count' => $resolved_count,
                    'disputes_count' => $disputes_count
                ];

        $collection = $this->collectDisputes($disputes_list);

        try {
            // Download the excel file
            return Excel::download(new DisputesExport($collection, $title), 'report_'.time().'.csv');

            return back()->withSuccess('Export started!');

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    private function preparePdfRuntime()
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
    }
}
