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
use App\Helpers\ContainerCountHelper as CCH;
use Carbon\Carbon;

class VesselOperatorWiseContainerHandling implements FromCollection, WithMapping, WithHeadings, WithEvents
{
    use Exportable;

    private $sq = 0;
    private $data, $range, $route;
    private $routes = [
        '1' => 1,
        '2' => 2,
        '3' => 3,
    ];
    private string $lastOperator = '';
    private int $rowIndex = 1;


    public function __construct($data,$route,$range)
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
        $rows = [];
        if ($this->lastOperator !== '' && $this->lastOperator !== $data->operator) {
            $rows[] = array_fill(0, 30, null);
            $this->rowIndex=1;
        }

        $this->lastOperator = $data->operator;

        $importTeu = CCH::calculateTeu($data->importExportCounts->where('type','import')->first() ?? (object)[]);
        $exportTeu = CCH::calculateTeu($data->importExportCounts->where('type','export')->first() ?? (object)[]);
        $importBox = CCH::calculateBox($data->importExportCounts->where('type','import')->first() ?? (object)[]);
        $exportBox = CCH::calculateBox($data->importExportCounts->where('type','export')->first() ?? (object)[]);

        $rows[] = [
            $this->rowIndex++,
            $data->vessel->vessel_name ?? '',
            Carbon::parse($data->arrival_date)->format('d.m.y'),
            Carbon::parse($data->berth_date)->format('d.m.y'),
            Carbon::parse($data->sail_date)->format('d.m.y'),
            $data->operator,
            $data->vessel->length_overall,
            $data->vessel->crane_status,
            $data->route->short_name ?? '',

            CCH::charZeroIfEmpty($data->importExportCounts->where('type','import')->first()->dc20),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','import')->first()->dc40),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','import')->first()->dc45),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','import')->first()->r20),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','import')->first()->r40),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','import')->first()->mty20),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','import')->first()->mty40),

            CCH::charZeroIfEmpty($importTeu['laden']),
            CCH::charZeroIfEmpty($importTeu['empty']),
            CCH::charZeroIfEmpty($importTeu['total']),

            CCH::charZeroIfEmpty($data->importExportCounts->where('type','export')->first()->dc20),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','export')->first()->dc40),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','export')->first()->dc45),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','export')->first()->r20),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','export')->first()->r40),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','export')->first()->mty20),
            CCH::charZeroIfEmpty($data->importExportCounts->where('type','export')->first()->mty40),

            CCH::charZeroIfEmpty($exportTeu['laden']),
            CCH::charZeroIfEmpty($exportTeu['empty']),
            CCH::charZeroIfEmpty($exportTeu['total']),
            CCH::charZeroIfEmpty($importBox['total'] + $exportBox['total'])
        ];

        return $rows;
    }

    public function headings(): array
    {
        return [
            [
                'OPT wise Container Lifting' . ($this->route?' | '.$this->route:''). ' | ' .$this->range
            ],
            [
                'Arrival Departure Info',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'IMPORT',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'EXPORT',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
            [
                'SL. NO.',
                'VSL NAME',
                'A/D',
                'BERTH',
                'SLD',
                'OPT',
                'LOA',
                'G/GL',
                'Route',
                '20\'',
                '40\'',
                '45\'',
                '20R',
                '40R',
                '20M',
                '40M',
                'LDN TEUs',
                'MTY TEUs',
                'TTL IMP TEUs',
                'E20\'',
                'E40\'',
                'E45\'',
                'E20R',
                'E40R',
                'E20M',
                'E40M',
                'LDN TEUS',
                'MTY TEUS',
                'TTL EXP TEUs',
                'TOTAL BOX'
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



                $event->sheet->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getDelegate()->freezePane('A4');

                $parent->getDefaultStyle()->getFont()->setSize(11);

                $sheet->mergeCells("A1:{$highestColumn}1");
                $sheet->mergeCells("A2:E2");
                $sheet->mergeCells("J2:R2");
                $sheet->mergeCells("T2:AB2");

                $event->sheet->getDelegate()->getStyle("A1:{$highestColumn}1")->getFont()->setSize(12)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A2:{$highestColumn}2")->getFont()->setSize(12)->setBold(true);
                $event->sheet->getDelegate()->getStyle("A3:{$highestColumn}3")->getFont()->setBold(true);
                $sheet->getStyle("A1:{$highestColumn}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $lastDataRow = $sheet->getHighestRow(); // after headings

                //Subtotal Area
                $subtotalRow = $lastDataRow + 1;
                $borderFull = "A1:{$highestColumn}{$subtotalRow}";
                $sheet->setCellValue("A{$subtotalRow}", 'GRAND TOTAL');
                $event->sheet->getDelegate()->getStyle("A{$subtotalRow}:{$highestColumn}{$subtotalRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$subtotalRow}:{$highestColumn}{$subtotalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->mergeCells("A{$subtotalRow}:E{$subtotalRow}");

                // $sheet->getStyle("A{$subtotalRow}")->getFont()->setBold(true);
                // $sheet->getStyle("A{$subtotalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $subtotalTitleColumns = ['B','C','D','E','F','G','H','I'];
                $subtotalColumns = [
                    'J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD'
                ];

                foreach ($subtotalTitleColumns as $col) {
                    $sheet->setCellValue("{$col}{$subtotalRow}", "");
                    $sheet->getStyle("{$col}{$subtotalRow}")->getFont()->setBold(true);
                }

                foreach ($subtotalColumns as $col) {
                    $sheet->setCellValue("{$col}{$subtotalRow}", "=SUM({$col}4:{$col}{$lastDataRow})");
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
