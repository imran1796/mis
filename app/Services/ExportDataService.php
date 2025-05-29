<?php

namespace App\Services;

use App\Interfaces\ExportDataInterface;
use App\Models\ExportData;
use App\Imports\ImportUser;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ExportDataService
{
    protected $exportDataRepository;

    public function __construct(ExportDataInterface $exportDataRepository)
    {
        $this->exportDataRepository = $exportDataRepository;
    }

    public function getAllExportData($filters)
    {
        if (!empty($filters['from_date'])) {
            $filters['from_date'] = Carbon::createFromFormat('M-Y', $filters['from_date'])->startOfMonth();
        }
        if (!empty($filters['to_date'])) {
            $filters['to_date'] = Carbon::createFromFormat('M-Y', $filters['to_date'])->startOfMonth();
        }
        // if(!isset($filters['report_type'])){
        //     return [collect(),collect()];
        // }

        $data =  $this->exportDataRepository->getAllExportData($filters);
        $summary = $this->exportDataSummary($data);
        $dynamicReport = isset($filters['report_type']) ? $this->prepare($data, $filters['report_type']) : null;

        return [$dynamicReport ?? $data, $summary];
    }

    private function prepare($data, $key)
    {
        $groupingOptions = [
            'mlo_wise' => ['mlo'],
            'commodity_wise' => ['commodity'],
            'mlo_commodity_wise' => ['mlo', 'commodity'],
            'pod_wise' => ['pod'],
            'mlo_pod_wise' => ['mlo', 'pod'],
            'pod_commodity_wise' => ['pod', 'commodity'],
            'region_wise' => ['trade'],
            'region_pod_wise' => ['trade', 'pod'],
            'region_commodity_pod_wise' => ['trade', 'commodity', 'pod'],
            'mlo_commodity_pod_wise' => ['mlo', 'commodity', 'pod'],
            'mlo_pod_commodity_wise' => ['mlo', 'pod', 'commodity'],
        ];

        $groupingKeys = $groupingOptions[$key] ?? ['mlo_wise'];

        $groupedData = [];

        $totalTEUs = 0;

        foreach ($data as $row) {
            $groupKey = [];
            foreach ($groupingKeys as $key) {
                $groupKey[] = $row->$key;
            }

            $groupKeyString = implode('_', $groupKey);

            if (!isset($groupedData[$groupKeyString])) {
                $groupedData[$groupKeyString] = array_merge(array_combine($groupingKeys, $groupKey), [
                    '20ft' => 0,
                    '40ft' => 0,
                    '45ft' => 0,
                    '20R' => 0,
                    '40R' => 0,
                    'unit_count' => 0,
                    'teus' => 0,
                    'teus_percentage' => 0
                ]);
            }

            foreach (['20ft', '40ft', '45ft', '20R', '40R'] as $size) {
                $groupedData[$groupKeyString][$size] += (int) $row->$size;
            }

            $groupedData[$groupKeyString]['unit_count'] +=
                (int) $row->{'20ft'} + (int) $row->{'40ft'} +
                (int) $row->{'45ft'} + (int) $row->{'20R'} + (int) $row->{'40R'};

            $groupedData[$groupKeyString]['teus'] +=
                ((int) $row->{'20ft'} * 1) + ((int) $row->{'40ft'} * 2) +
                ((int) $row->{'45ft'} * 2) + ((int) $row->{'20R'} * 1) + ((int) $row->{'40R'} * 2);

            // $totalTEUs += $groupedData[$groupKeyString]['teus'];
        }

        foreach ($groupedData as $i) {
            $totalTEUs += $i['teus'];
        }

        // dd($groupedData,$totalTEUs);

        foreach ($groupedData as &$row) {
            $row['teus_percentage'] = $totalTEUs > 0
                ? round(($row['teus'] / $totalTEUs) * 100, 2)
                : 0;
        }

        usort($groupedData, function ($a, $b) {
            return $b['teus'] <=> $a['teus'];
        });

        return [$groupedData, $groupingKeys, $key];
    }


    private function exportDataSummary($data)
    {
        return [
            '20ft' => $data->sum('20ft'),
            '40ft' => $data->sum('40ft'),
            '45ft' => $data->sum('45ft'),
            '20R' => $data->sum('20R'),
            '40R' => $data->sum('40R'),
            'total_unit' => $data->sum('20ft') + $data->sum('40ft') + $data->sum('45ft') + $data->sum('20R') + $data->sum('40R'),
            'total_teus' => ($data->sum('20ft') * 1) + ($data->sum('40ft') * 2) + ($data->sum('45ft') * 2) + ($data->sum('20R') * 1) + ($data->sum('40R') * 2),
            'total_commodity' => $data->pluck('commodity')->map(fn($commodity) => trim($commodity))->unique()->count(),
            'total_pod' => $data->pluck('pod')->map(fn($pod) => trim($pod))->unique()->count(),
        ];
    }


    public function getUniqueExportData($column,$value=null)
    {
        return $this->exportDataRepository->getUniqueExportData($column,$value);
    }


    public function createExportData($request)
    {
        $date = Carbon::createFromFormat('M-Y', $request->date)->startOfMonth();

        $existing = ExportData::whereDate('date', $date)->exists();
        if ($existing) {
            return response()->json(['error' => "Data for this Date already exist: "], 409);
        }

        // $array = Excel::toArray(new ImportUser, $request->file('file'));
        $array = Excel::toArray([], $request->file('file'));
        $array = $array[0];
        $array2[0] = array_slice($array, 2);
        // dd($array2[0],$array[0]);

        $data = [];
        foreach ($array2[0] as $a) {
            if (strtoupper($a[1]) === 'G.TOTAL') {
                break;
            }

            $data[] = [
                'mlo' => strtoupper(trim($a[1])),
                '20ft' => trim($a[2]) ?: null,
                '40ft' => trim($a[3]) ?: null,
                '45ft' => trim($a[4]) ?: null,
                '20R' => trim($a[5]) ?: null,
                '40R' => trim($a[6]) ?: null,
                'commodity' => strtoupper(trim($a[7])),
                'pod' => strtoupper(trim($a[8])),
                'trade' => strtoupper(trim($a[9])),
                'port_code' => strtoupper(trim($a[10]??'')),
                'date' => $date,
            ];
        }

        return $this->exportDataRepository->createExportData($data);
    }

    public function exportVolByRegion($filters)
    {
        $x = [];
        $data = collect($this->getAllExportData($filters)[0])->groupBy('trade');
        foreach ($data as $region => $records) {
            $byPort = $records->groupBy('pod');
            foreach ($byPort as $port => $r) {
                $sum20 = $r->sum('20ft') + $r->sum('20R');
                $sum40 = ($r->sum('40ft') + $r->sum('45ft') + $r->sum('40R')) * 2;

                $totalTeu = $sum20 + $sum40;
                $mloShare = $this->topShare($r, 'mlo', $totalTeu);
                $commodityShare = $this->topShare($r, 'commodity', $totalTeu);
                $x[$region][$port] = [
                    '20ft'       => $sum20,
                    '40ft'       => $sum40,
                    'total_teu'  => $totalTeu,
                    'mlo_share'  => $mloShare,
                    'commodities' => $commodityShare,
                ];
            }
        }
        // dd($x);
        return $x;
    }

    public function exportVolByPort($filters)
    {
        $data = collect($this->getAllExportData($filters)[0])->groupBy('pod');

        foreach ($data as $pod => $records) {
            $sum20 = $records->sum('20ft') + $records->sum('20R');
            $sum40 = ($records->sum('40ft') + $records->sum('45ft') + $records->sum('40R')) * 2;

            $totalTeu = $sum20 + $sum40;
            $mloShare = $this->topShare($records, 'mlo', $totalTeu);
            $commodityShare = $this->topShare($records, 'commodity', $totalTeu);

            // $snkMetrics = $this->calculateSknMetrics($records, $totalTeu);
            $snkTeus = $records->where('mlo', 'SKN')->sum(fn($item) => ($item->{'20ft'} ?? 0) + ($item->{'20R'} ?? 0) + ($item->{'40R'} ? $item->{'40R'} * 2 : 0) + ($item->{'40ft'} ? $item->{'40ft'} * 2 : 0) + ($item->{'45ft'} ? $item->{'45ft'} * 2 : 0));

            $snkShare = $totalTeu > 0 ? round(($snkTeus / $totalTeu) * 100, 1) : 0;

            $x[$pod] = [
                'pol'        => 'BDCGP',
                'pod'        => $pod,
                '20ft'       => $sum20,
                '40ft'       => $sum40,
                'total_teu'  => $totalTeu,
                '20_ratio'   => $totalTeu > 0 ? round(($sum20 / $totalTeu) * 100) . '%' : '0%',
                '40_ratio'   => $totalTeu > 0 ? round(($sum40 / $totalTeu) * 100) . '%' : '0%',
                // 'snkTeus'     => $snkMetrics['teus'],
                // 'snkShare'    => $snkMetrics['share'] . '%',
                'snkTeus'     => $snkTeus,
                'snkShare'    => $snkShare . '%',
                'mlo_share'  => $mloShare,
                'commodities' => $commodityShare,
            ];
        }
        // dd($x['NINGBO']);
        return $x;
    }

    protected function calculateSknMetrics(Collection $records, int $totalTeu): array
    {
        $sknTeus = $records->where('mlo', 'SKN')->sum(
            fn($r) =>
            (int) $r->teu_20 + (int) $r->teu_20R + (int) $r->teu_40 * 2 + (int) $r->teu_45 * 2 + (int) $r->teu_40R * 2
        );

        return [
            'teus'  => $sknTeus,
            'share' => $totalTeu > 0 ? round(($sknTeus / $totalTeu) * 100, 1) : 0,
        ];
    }

    protected function topShare(Collection $records, string $groupBy, int $totalTeu)
    {

        return $records->groupBy($groupBy)->mapWithKeys(function ($group) use ($totalTeu, $groupBy) {
            $groupByTeu = $this->calculateTeu($group);

            $percent = $totalTeu > 0 ? round(($groupByTeu / $totalTeu) * 100, 1) : 0;
            $groupByName = $group->first()->$groupBy;

            return [$groupByName => $percent];
        })
            ->sortDesc()
            ->take(5)
            ->map(function ($percent, $groupByName) {
                return "{$groupByName}({$percent}%)";
            })
            ->implode(', ');
    }

    protected function calculateTeu($record)
    {
        return $record->sum('20ft') + $record->sum('20R') + (($record->sum('40ft') + $record->sum('45ft') + $record->sum('40R')) * 2);
    }
}
