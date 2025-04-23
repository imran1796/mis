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
            $data['effective_capacity'] ?? 0,
            $data['nominal_capacity'] ?? 0,
            $data['import'] ?? 0,
            $data['export_laden'] ?? 0,
            $data['export_empty'] ?? 0,
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

                // Merge headers
                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->mergeCells("A3:{$highestColumn}3");
                $sheet->mergeCells("A4:{$highestColumn}4");

                $totalsRow = $highestRow + 1;
                $sheet->setCellValue("A{$totalsRow}", 'Total');
                $sheet->mergeCells("A{$totalsRow}:B{$totalsRow}");
                $sheet->getStyle("A{$totalsRow}")->getFont()->setBold(true);

                $columnsToTotal = ['C', 'D', 'G', 'H', 'K', 'L', 'M', 'N'];

                foreach ($columnsToTotal as $col) {
                    $sheet->setCellValue("{$col}{$totalsRow}", "=SUM({$col}5:{$col}{$highestRow})");
                    $sheet->getStyle("{$col}{$totalsRow}")->getFont()->setBold(true);
                }

                // Style headers
                foreach ([1, 2, 3] as $row) {
                    $sheet->getStyle("A{$row}:{$highestColumn}{$row}")->getFont()->setBold(true)->setSize(13);
                }

                // Borders
                $sheet->getStyle("A5:{$highestColumn}{$highestRow}")->applyFromArray([
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
