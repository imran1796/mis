<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SocInOutBound implements WithHeadings, WithEvents, FromArray
{
    protected $data;
    protected $range;
    protected $route;

    public function __construct($data, $range, $route)
    {
        $this->data = $data;
        $this->range = $range;
        $this->route = $route;
        dd($data);
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->data as $month => $data) {
            $importLadenTeus = 0;
            $importEmptyTeus = 0;
            $exportLadenTeus = 0;
            $exportEmptyTeus = 0;

            $import = [];
            foreach (['20', '40', '45', '20R', '40R', 'MTY20', 'MTY40'] as $col) {
                $val = $data['import'][$col];
                $import[] = $val;
                $importLadenTeus += in_array($col, ['40', '45', '40R']) ? $val * 2 : ($col === 'MTY20' || $col === 'MTY40' ? 0 : $val);
                $importEmptyTeus += $col === 'MTY40' ? $val * 2 : ($col === 'MTY20' ? $val : 0);
            }
            $import[] = $importLadenTeus;
            $import[] = $importEmptyTeus;
            $import[] = $importLadenTeus + $importEmptyTeus;

            $export = [];
            foreach (['20', '40', '45', '20R', '40R', 'MTY20', 'MTY40'] as $col) {
                $val = $data['export'][$col];
                $export[] = $val;
                $exportLadenTeus += in_array($col, ['40', '45', '40R']) ? $val * 2 : ($col === 'MTY20' || $col === 'MTY40' ? 0 : $val);
                $exportEmptyTeus += $col === 'MTY40' ? $val * 2 : ($col === 'MTY20' ? $val : 0);
            }
            $export[] = $exportLadenTeus;
            $export[] = $exportEmptyTeus;
            $export[] = $exportLadenTeus + $exportEmptyTeus;

            $calls = $data['calls'];
            $vessels = $data['vessels_count'];

            $rows[] = array_merge([
                $month,
            ], $import, $export, [
                $calls['SIN'],
                $calls['CBO'],
                $calls['CGP'],
                $calls['SIN'] + $calls['CBO'] + $calls['CGP'],
                $vessels['SIN'],
                $vessels['CBO'],
                $vessels['CGP'],
                $vessels['SIN'] + $vessels['CBO'] + $vessels['CGP'],
            ]);
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            ['Sinokor Merchant Marine Co., Ltd.'],
            ['Globe Link Associates Ltd.'],
            ['Soc In/Out Bound ' . $this->range . $this->route? ' | ' . $this->route : ''],
            [''],
            array_merge(
                ['Month'],
                array_fill(0, 10, 'Import'),
                array_fill(0, 10, 'Export'),
                ['Total Call','','', 'Total Call'],
                ['Total Vessel','','', 'Total Vessel']
            ),
            array_merge(
                ['Month'],
                ['20', '40', '45', '20R', '40R', 'MTY20', 'MTY40', 'LDN Teus', 'MTY Teus', 'Total'],
                ['20', '40', '45', '20R', '40R', 'MTY20', 'MTY40', 'LDN Teus', 'MTY Teus', 'Total'],
                ['SIN', 'CBO', 'CGP', 'Total'],
                ['SIN', 'CBO', 'CGP', 'Total']
            ),
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
                $highestRow = $sheet->getHighestRow() + 1;
                $range = "A1:{$highestColumn}{$highestRow}";

                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->mergeCells("A3:{$highestColumn}3");
                $sheet->mergeCells("A4:{$highestColumn}4");
                $sheet->mergeCells("B5:K5");
                $sheet->mergeCells("L5:U5");
                $sheet->mergeCells("V5:X5");
                $sheet->mergeCells("Z5:AB5");
                $sheet->mergeCells("Y5:Y6");
                $sheet->mergeCells("AC5:AC6");
                $sheet->mergeCells("A5:A6");

                $event->sheet->getDelegate()->getStyle("A1:{$highestColumn}1")->getFont()->setSize(13)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A2:{$highestColumn}2")->getFont()->setSize(13)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A3:{$highestColumn}3")->getFont()->setSize(13)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A5:{$highestColumn}5")->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle("A6:{$highestColumn}6")->getFont()->setBold(true);
            },
        ];
    }
}
