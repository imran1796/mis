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


class OperatorWiseSummary implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    use Exportable;

    private $sq = 1;
    private $data;
    private $data2;

    public function __construct($data, $data2)
    {
        $this->data = $data;
        $this->data2 = $data2;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function map($data): array
    {
        return [
            $this->sq++,
            $data['operator'] ?? '',

            $data['total_laden_import'] ?? '',
            $data['total_empty_import'] ?? '',
            $data['import_laden_eff'] ?? '',
            $data['import_empty_eff'] ?? '',

            $data['total_laden_export'] ?? '',
            $data['total_empty_export'] ?? '',
            $data['export_laden_eff'] ?? '',
            $data['export_empty_eff'] ?? '',

            $data['vessel_calls'] ?? '',
            $data['unique_vessels'] ?? '',
            $data['effective_capacity'] ?? '',
            $data['nominal_capacity'] ?? '',
            $data['import'] ?? '',
            $data['export_laden'] ?? '',
            $data['export_empty'] ?? '',
        ];
    }


    public function headings(): array
    {
        return [
            ['Sinokor Merchant Marine Co., Ltd.'],
            ['Globe Link Associates Ltd.'],
            ['Operator Wise Container Lifting (Summary)'],
            [''],

            // Grouped Header for Operator-wise Analysis
            [
                'Sl',
                'Operator',
                'Import Laden TEU',
                'Import Empty TEU',
                'Import Laden Eff%',
                'Import Empty Eff%',
                'Export Laden TEU',
                'Export Empty TEU',
                'Export Laden Eff%',
                'Export Empty Eff%',
                'T. VSL Call',
                'T. VSL Handled',
                'Eff. Capacity',
                'Nom. Capacity',
                'Import %',
                'Export Laden %',
                'Export Empty %'
            ],

            // Empty line
            // [''],

            // Load Factor Table Headers
            // ['Load Factor Summary'],
            // [''],
            // ['Type', 'Effective Capacity %', 'Nominal Capacity %'],
            // ['Import Laden', $this->data2['import_laden_load_factor_effective'], $this->data2['import_laden_load_factor_nominal']],
            // ['Import Empty', $this->data2['import_empty_load_factor_effective'], $this->data2['import_empty_load_factor_nominal']],
            // ['Export Laden', $this->data2['export_laden_load_factor_effective'], $this->data2['export_laden_load_factor_nominal']],
            // ['Export Empty', $this->data2['export_empty_load_factor_effective'], $this->data2['export_empty_load_factor_nominal']],
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $parent = $sheet->getParent();

                

                // $event->sheet->getColumnDimension('B')->setAutoSize(true);
                // $event->sheet->getColumnDimension('C')->setWidth(25);
                // $event->sheet->getColumnDimension('J')->setWidth(25);


                $parent->getDefaultStyle()->getFont()->setSize(11);

                $parent->getDefaultStyle()->getAlignment()->applyFromArray([
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]);

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow() + 1;
                $range = "A1:{$highestColumn}{$highestRow}";

                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->mergeCells("A3:{$highestColumn}3");
                $sheet->mergeCells("A4:{$highestColumn}4");

                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->mergeCells("A6:R6"); // 'Load Factor Summary'
                $event->sheet->getDelegate()->getStyle("A6")->getFont()->setBold(true)->setSize(12);


                $event->sheet->getDelegate()->getStyle("A1:{$highestColumn}1")->getFont()->setSize(13)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A2:{$highestColumn}2")->getFont()->setSize(13)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A3:{$highestColumn}3")->getFont()->setSize(13)->setBold(true);
                // $event->sheet->getDelegate()->getStyle("A5:{$highestColumn}5")->getFont()->setSize(12)->setBold(true);
                // $event->sheet->getDelegate()->getStyle("A6:{$highestColumn}6")->getFont()->setSize(12)->setBold(true);
                // $event->sheet->getDelegate()->getStyle("A7:{$highestColumn}7")->getFont()->setSize(12)->setSize(12)->setBold(true);
                // $event->sheet->getDelegate()->getStyle("A9:{$highestColumn}9")->getFont()->setSize(12)->setBold(true);

                // $lastRow = $event->sheet->getHighestRow();
                // $event->sheet->setCellValue("A{$lastRow}", "Total");
                // $event->sheet->mergeCells("A{$lastRow}:C{$lastRow}");
                // $event->sheet->mergeCells("F{$lastRow}:M{$lastRow}");
                // $event->sheet->setCellValue('D'. ($event->sheet->getHighestRow()), '=SUM(D9:D'.($lastRow-1).')');
                // $event->sheet->setCellValue('E'. ($event->sheet->getHighestRow()), '=SUM(E9:E'.($lastRow-1).')');
                // // $event->sheet->setCellValue("C{$lastRow}", 12);

                $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
