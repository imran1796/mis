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

class OperatorWiseSummary implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    use Exportable;

    private int $sq = 1;
    private $data;
    private $data2;
    private string $range;
    private string $route;

    public function __construct($data,  $data2, string $range, string $route)
    {
        $this->data = $data;
        $this->data2 = $data2;
        $this->range = $range;
        $this->route = $route;
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
            $data['total_laden_import'] ?? 0,
            $data['total_empty_import'] ?? 0,
            $data['import_laden_eff'] ?? 0,
            $data['import_empty_eff'] ?? 0,
            $data['total_laden_export'] ?? 0,
            $data['total_empty_export'] ?? 0,
            $data['export_laden_eff'] ?? 0,
            $data['export_empty_eff'] ?? 0,
            $data['vessel_calls'] ?? 0,
            $data['unique_vessels'] ?? 0,
            $data['effective_capacity'] ?round($data['effective_capacity']): 0,
            $data['nominal_capacity'] ?? 0,
            $data['import'] ?round($data['import'],1).'%': 0,
            $data['export_laden'] ?round($data['export_laden'],1).'%': 0,
            $data['export_empty'] ?round($data['export_empty'],1).'%': 0,
        ];
    }

    public function headings(): array
    {
        return [
            ['Sinokor Merchant Marine Co., Ltd.'],
            ['Globe Link Associates Ltd.'],
            ['Operator Wise Container Lifting (Summary) ' . $this->range . ' - ' . strtoupper($this->route)],
            [''],
            [
                'SL', 'OPERATOR',
                'IMPORT', '', '', '',
                'EXPORT', '', '', '',
                'T. VSL Call',
                'T. VSL Handled',
                'Eff. Capacity',
                'Nom. Capacity',
                'Import %',
                'Export Laden %',
                'Export Empty %'
            ],
            [
                '', '', 
                'Laden TEUs', 'Empty TEUs', 'Laden Eff%', 'Empty Eff%',
                'Laden TEUs', 'Empty TEUs', 'Laden Eff%', 'Empty Eff%',
                '', '', '', '', '', '', ''
            ],
            // [
            //     'Sl',
            //     'Operator',
            //     'Import Laden TEU',
            //     'Import Empty TEU',
            //     'Import Laden Eff%',
            //     'Import Empty Eff%',
            //     'Export Laden TEU',
            //     'Export Empty TEU',
            //     'Export Laden Eff%',
            //     'Export Empty Eff%',
            //     'T. VSL Call',
            //     'T. VSL Handled',
            //     'Eff. Capacity',
            //     'Nom. Capacity',
            //     'Import %',
            //     'Export Laden %',
            //     'Export Empty %'
            // ],
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
                $range = "A1:{$highestColumn}{$highestRow}";

                $sheet->getStyle('A5:Q6')->getFont()->setBold(true);

                // Merge headers
                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->mergeCells("A3:{$highestColumn}3");
                $sheet->mergeCells("A4:{$highestColumn}4");

                $sheet->mergeCells('A5:A6');
                $sheet->mergeCells('B5:B6');
                $sheet->mergeCells('C5:F5');
                $sheet->mergeCells('G5:J5');
                $sheet->mergeCells('K5:K6');
                $sheet->mergeCells('L5:L6');
                $sheet->mergeCells('M5:M6');
                $sheet->mergeCells('N5:N6');
                $sheet->mergeCells('O5:O6');
                $sheet->mergeCells('P5:P6');
                $sheet->mergeCells('Q5:Q6');
                $sheet->getStyle('O5:Q6')
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFF5F5F5');

                $totalsRow = $highestRow + 1;
                $sheet->setCellValue("A{$totalsRow}", 'Total');
                $sheet->mergeCells("A{$totalsRow}:B{$totalsRow}");
                $sheet->getStyle("A{$totalsRow}")->getFont()->setBold(true);

                $columnsToTotal = ['C', 'D', 'G', 'H', 'K', 'L', 'M', 'N'];
                $columnsTo100 = ['O', 'P', 'Q'];

                foreach ($columnsToTotal as $col) {
                    $sheet->setCellValue("{$col}{$totalsRow}", "=SUM({$col}5:{$col}{$highestRow})");
                    $sheet->getStyle("{$col}{$totalsRow}")->getFont()->setBold(true);
                }
                foreach ($columnsTo100 as $col) {
                    $sheet->setCellValue("{$col}{$totalsRow}", "100%");
                    $sheet->getStyle("{$col}{$totalsRow}")->getFont()->setBold(true);
                }

                // Style headers
                foreach ([1, 2, 3] as $row) {
                    $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->getFont()->setBold(true)->setSize(13);
                }

                // Borders
                $sheet->getStyle("A5:{$highestColumn}{$totalsRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // ========== Load Factor Table ==========
                $loadFactorStartRow = $highestRow + 3;
                $sheet->setCellValue("A{$loadFactorStartRow}", 'Load Factor Summary');
                $sheet->mergeCells("A{$loadFactorStartRow}:F{$loadFactorStartRow}");
                $sheet->getStyle("A{$loadFactorStartRow}")->getFont()->setBold(true)->setSize(12);

                // Header row
                $headersRow = $loadFactorStartRow + 1;
                $sheet->setCellValue("A{$headersRow}", '');
                $sheet->setCellValue("B{$headersRow}", 'IMP LDN');
                $sheet->setCellValue("C{$headersRow}", 'IMP MTY');
                $sheet->setCellValue("D{$headersRow}", '');
                $sheet->setCellValue("E{$headersRow}", 'EXP LDN');
                $sheet->setCellValue("F{$headersRow}", 'EXP MTY');

                // Style header
                $sheet->getStyle("A{$headersRow}:F{$headersRow}")->getFont()->setBold(true);

                // Rows
                $rows = [
                    ['Eff Capacity', 'imp_ldn_lf_eff', 'imp_mty_lf_eff', 'exp_ldn_lf_eff', 'exp_mty_lf_eff'],
                    ['Nom Capacity', 'imp_ldn_lf_nom', 'imp_mty_lf_nom', 'exp_ldn_lf_nom', 'exp_mty_lf_nom'],
                ];

                foreach ($rows as $i => [$label, $key1, $key2, $key3, $key4]) {
                    $row = $headersRow + 1 + $i;
                    $sheet->setCellValue("A{$row}", $label);
                    $sheet->setCellValue("B{$row}", $this->data2[$key1] ?? 0);
                    $sheet->setCellValue("C{$row}", $this->data2[$key2] ?? 0);
                    // $sheet->setCellValue("D{$row}", $this->data2[$key2] ?? 0);
                    $sheet->setCellValue("E{$row}", $this->data2[$key3] ?? 0);
                    $sheet->setCellValue("F{$row}", $this->data2[$key4] ?? 0);
                }

                // Borders and alignment
                $endRow = $headersRow + count($rows);
                $sheet->getStyle("A{$headersRow}:F{$endRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
            },
        ];
    }
}
