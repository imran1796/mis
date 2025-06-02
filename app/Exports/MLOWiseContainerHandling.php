<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Helpers\ContainerCountHelper as CCH;

class MLOWiseContainerHandling implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    use Exportable;

    private $data, $range, $route;


    public function __construct($data, $route, $range)
    {
        $this->data = $data;
        $this->route = $route;
        $this->range = $range;
    }

    public function collection()
    {
        return collect($this->data);
        
    }

    public function map($data): array
    {
        // dd($data);
        $import = $data->where('type', 'import')->first();
        $export = $data->where('type', 'export')->first();

        $importTeu = CCH::calculateTeu($import ?? (object)[]);
        $exportTeu = CCH::calculateTeu($export ?? (object)[]);

        return [
            $import?->mlo_code ?? $export?->mlo_code ?? '-',
            CCH::zeroIfEmpty($import->dc20 ?? null),
            CCH::zeroIfEmpty($import->dc40 ?? null),
            CCH::zeroIfEmpty($import->dc45 ?? null),
            CCH::zeroIfEmpty($import->r20 ?? null),
            CCH::zeroIfEmpty($import->r40 ?? null),
            CCH::zeroIfEmpty($import->mty20 ?? null),
            CCH::zeroIfEmpty($import->mty40 ?? null),
            $importTeu['laden'] ?? 0,
            $importTeu['empty'] ?? 0,
            (($importTeu['laden']??0)+($importTeu['empty']??0)),

            CCH::zeroIfEmpty($export->dc20 ?? null),
            CCH::zeroIfEmpty($export->dc40 ?? null),
            CCH::zeroIfEmpty($export->dc45 ?? null),
            CCH::zeroIfEmpty($export->r20 ?? null),
            CCH::zeroIfEmpty($export->r40 ?? null),
            CCH::zeroIfEmpty($export->mty20 ?? null),
            CCH::zeroIfEmpty($export->mty40 ?? null),
            $exportTeu['laden'] ?? 0,
            $exportTeu['empty'] ?? 0,
            (($exportTeu['laden']??0)+($exportTeu['empty']??0)),
        ];
    }

    public function headings(): array
    {
        return [
            ['MLO Wise Container Handling for '.$this->range],
            ['Route: '.$this->route],
            ['','IMPORT', '', '', '', '', '', '', '', '', 'TTL IMP. TEUs', 'EXPORT', '', '', '', '', '', '', '', '', 'TTL EXP. TEUs'],
            ['MLO', '20\'', '40\'', '45\'', '20R', '40R', '20M', '40M', 'LDN TEUs', 'MTY TEUs', '', '20\'', '40\'', '45\'', '20R', '40R', '20M', '40M', 'LDN TEUs', 'MTY TEUs','']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $parent = $sheet->getParent();

                $parent->getDefaultStyle()->getFont()->setSize(11);
                $parent->getDefaultStyle()->getAlignment()->applyFromArray([
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]);

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $event->sheet->getDelegate()->freezePane('A5');

                $sheet->mergeCells("A1:U1");
                $sheet->mergeCells("A2:U2");
                $sheet->mergeCells("B3:J3");
                $sheet->mergeCells("L3:T3");
                $sheet->mergeCells('K3:K4');
                $sheet->mergeCells('U3:U4');
                $sheet->getStyle('A1:U4')->getFont()->setBold(true);
                $sheet->getStyle('A1:U4')->getAlignment()->setHorizontal('center')->setVertical('center');


                $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $lastDataRow = $sheet->getHighestRow(); // after headings

                //Subtotal Area
                $subtotalRow = $lastDataRow + 1;
                $borderFull = "A1:{$highestColumn}{$subtotalRow}";

                $sheet->setCellValue("A{$subtotalRow}", 'G.TOTAL');
                $event->sheet->getDelegate()->getStyle("A{$subtotalRow}:{$highestColumn}{$subtotalRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$subtotalRow}:{$highestColumn}{$subtotalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $subtotalColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K','L','M','N','O','P','Q','R','S','T','U'];
            
                foreach ($subtotalColumns as $col) {
                    $sheet->setCellValue("{$col}{$subtotalRow}", "=SUM({$col}5:{$col}{$lastDataRow})");
                    $sheet->getStyle("{$col}{$subtotalRow}")->getFont()->setBold(true);
                }

                $sheet->getStyle($borderFull)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
