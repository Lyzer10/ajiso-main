<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class DisputesExport implements FromArray, WithHeadings, WithStrictNullComparison, ShouldAutoSize
{
    use Exportable;

    protected $disputes;

    public function __construct(array $disputes, array $title)
    {
        $this->disputes = $disputes;
        $this->title = $title;
    }

    public function array(): array
    {
        return $this->disputes;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            [
                'Results Summary',
                'Date : '.$this->title['date_raw'].' ',
                'Filter : '.$this->title['filter_by'].' ',
                'Query : '.$this->title['filter_val'].' ',
                'Disputes Found : '.$this->title['disputes_count'].' ',
                'Resolved Disputes : '.$this->title['resolved_count']
            ],
            [],
            [
                'Id', 'Dispute No', 'Date Reported', 'Beneficiary', 'Legal Aid Provider',
                'Type of service','Type of case', 'Dispute status',
            ]
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}
