<?php

namespace App\Exports;

use App\Enums\RegionCode;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RegionWiseExportCounts implements FromArray, WithHeadings, WithEvents
{
    use Exportable;

    private array $data;
    private $range;
    private $sq = 0;

    public function __construct(array $data,$range)
    {
        $this->data = $data;
        $this->range = $range;
    }

    public function array(): array
    {
        $output = [];

        $output[] = ['Sinokor Merchant Marine Co., Ltd.'];
        $output[] = ['Globe Link Associates Ltd.'];
        $output[] = ['Volume of Global Ports '.$this->range];
        $output[] = [''];

        foreach ($this->data as $region => $ports) {
            $enum = RegionCode::fromCode($region);
            $label = $enum ? $enum->label() : 'Unknown';

            $output[] = ["{$region} : {$label}"];
            $output[] = ['POD', '20ft', '40ft', 'Total TEU', 'MLO Share', 'Commodities'];

            foreach ($ports as $pod => $values) {
                $output[] = [
                    $pod,
                    $values['20ft'],
                    $values['40ft']/2,
                    $values['total_teu'],
                    $values['mlo_share'],
                    $values['commodities'],
                ];
            }
            $output[] = [''];
        }

        return $output;
    }

    public function headings(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $rows = $this->array();
                $rowIndex = 1;

                $parent = $sheet->getParent();

                $parent->getDefaultStyle()->getFont()->setSize(11);
                $parent->getDefaultStyle()->getAlignment()->applyFromArray([
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]);

                foreach ([1, 2, 3] as $titleRow) {
                    $sheet->mergeCells("A{$titleRow}:F{$titleRow}");
                    $sheet->getStyle("A{$titleRow}")->getFont()->setBold(true)->setSize(13);
                }

                $rowIndex = 5;

                foreach ($this->data as $region => $ports) {
                    $prevRow = $rowIndex - 1;
                    $sheet->mergeCells("A{$rowIndex}:F{$rowIndex}");
                    $sheet->mergeCells("A{$prevRow}:F{$prevRow}");
                    $sheet->getStyle("A{$rowIndex}")->getFont()->setBold(true)->setSize(12);
                    $rowIndex++;

                    $sheet->getStyle("A{$rowIndex}:F{$rowIndex}")->getFont()->setBold(true)->setSize(12);
                    $rowIndex++;

                    foreach ($ports as $pod => $values) {
                        $sheet->getStyle("A{$rowIndex}:F{$rowIndex}")->getFont()->setSize(10);
                        $rowIndex++;
                    }

                    $rowIndex++;
                }

                $sheet->getColumnDimension('E')->setWidth(30); 
                $sheet->getColumnDimension('F')->setWidth(30); 

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();
                $range = "A1:{$highestColumn}{$highestRow}";

                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
