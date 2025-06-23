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
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class ExportMarketScenarioByPort implements FromCollection, WithMapping, WithHeadings, WithEvents, WithColumnFormatting
{
    use Exportable;

    private $sq = 1;
    private $data;
    private $range;
    private $previousShipperName = null;
    private $previousRow = 0;

    public function __construct($data, $range)
    {
        $this->data = $data;
        $this->range = $range;
    }

    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_PERCENTAGE_00,
            'K' => NumberFormat::FORMAT_PERCENTAGE_00,
            'M' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function map($data): array
    {

        return [
            $this->sq++,
            $data['pol'],
            $data['pod'],
            '',
            $data['teuAvgMonth'],
            $data['total_teu'],
            $data['total_box'],
            $data['20ft'],
            $data['40ft'],
            $data['20_ratio'],
            $data['40_ratio'],
            $data['snkTeus'],
            $data['snkShare'],
            $data['mlo_share'],
            $data['commodities']
        ];
    }

    public function headings(): array
    {
        return [
            ['Sinokor Merchant Marine Co., Ltd.'],
            ['Globe Link Associates Ltd.'],
            ['MONTHLY EXPORT VOLUME (EX BDCGP) REPORT BASIS ON SINOKOR EXISTING SERVICE PORTS ' . $this->range],
            [''],
            [
                'SL',
                'POL ',
                'POD ',
                'Country Name ',
                'MKT Size AVG(TEU/M) ',
                'MKT Size (TEU) ',
                'MKT Size (BOX)',
                '20\'L ',
                '40\'L ',
                '20\'Ratio ',
                '40\'Ratio ',
                'SNK Teus ',
                'SNK Share ',
                'Major Competetors ',
                'Commodity',
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $parent = $sheet->getParent();
                $event->sheet->getColumnDimension('B')->setAutoSize(true);

                $sheet->getColumnDimension('N')->setWidth(30);
                $sheet->getColumnDimension('O')->setWidth(30);
                $parent->getDefaultStyle()->getFont()->setSize(11);
                $parent->getDefaultStyle()->getAlignment()->applyFromArray([
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]);

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();
                $hightRowWithSum = $highestRow + 1;
                $range = "A1:{$highestColumn}{$hightRowWithSum}";


                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->mergeCells("A3:{$highestColumn}3");
                $sheet->mergeCells("A4:{$highestColumn}4");

                $event->sheet->getDelegate()->getStyle("A1:{$highestColumn}1")->getFont()->setSize(12)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A2:{$highestColumn}2")->getFont()->setSize(12)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A3:{$highestColumn}3")->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle("A5:{$highestColumn}5")->getFont()->setBold(true);
                $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $lastDataRow = $sheet->getHighestRow(); // after headings
                $subtotalRow = $lastDataRow + 1;

                // Set label in first cell
                $sheet->setCellValue("A{$subtotalRow}", 'TOTAL');

                // Optional: Bold and center the label
                $sheet->getStyle("A{$subtotalRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$subtotalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Define columns to total up (based on headings layout)
                $sumColumns = [
                    'E', //total box
                    'F', // teu/ttl month
                    'G', // total_teu
                    'H', // 20ft
                    'I', // 40ft
                    'L', // snkTeus
                    // 'J', // 20Ratio
                    // 'K', // 40Ratio
                    // 'M', // Snk Share
                ];

                foreach ($sumColumns as $col) {
                    $sheet->setCellValue("{$col}{$subtotalRow}", "=SUM({$col}6:{$col}{$lastDataRow})");
                    $sheet->getStyle("{$col}{$subtotalRow}")->getFont()->setBold(true);
                }
                $sheet->mergeCells("A{$hightRowWithSum}:D{$hightRowWithSum}");
                $sheet->mergeCells("N{$hightRowWithSum}:O{$hightRowWithSum}");
                $sheet->getStyle("A{$hightRowWithSum}:D{$hightRowWithSum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}
