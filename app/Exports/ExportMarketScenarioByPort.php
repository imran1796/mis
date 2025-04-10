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


class ExportMarketScenarioByPort implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    use Exportable;

    private $sq = 0;
    private $data;
    private $range;
    private $previousShipperName = null;
    private $previousRow = 0;

    public function __construct($data, $range)
    {
        $this->data = $data;
        $this->range = $range;
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
            '','',
            $data['total_teu'],
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
            ['MONTHLY EXPORT VOLUME (EX BDCGP) REPORT BASIS ON SINOKOR EXISTING SERVICE PORTS '.$this->range],
            [''],
            [
                'SL',
                'POL ',
                'POD ',
                'Country Name ',
                'MKT Size (AVG) ',
                'MKT Size (Teu/M) ',
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
                
                $sheet->getColumnDimension('M')->setWidth(30);
                $sheet->getColumnDimension('N')->setWidth(30);
                $parent->getDefaultStyle()->getFont()->setSize(11);
                $parent->getDefaultStyle()->getAlignment()->applyFromArray([
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ]);

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
                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:{$highestColumn}2");
                $sheet->mergeCells("A3:{$highestColumn}3");
                $sheet->mergeCells("A4:{$highestColumn}4");
                $event->sheet->getDelegate()->getStyle("A1:{$highestColumn}1")->getFont()->setSize(12)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A2:{$highestColumn}2")->getFont()->setSize(12)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A3:{$highestColumn}3")->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle("A5:{$highestColumn}5")->getFont()->setBold(true);
                $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                
            },
        ];
    }
}
