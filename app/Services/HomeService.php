<?php

namespace App\Services;

use App\Models\MloWiseCount;
use App\Helpers\ContainerCountHelper;
use App\Helpers\GetUniqueYearHelper;
use App\Helpers\StaticDataHelper;
use App\Interfaces\VesselInfoInterface;
use App\Models\ExportData;
use App\Models\ImportExportCount;
use App\Models\VesselInfos;
use App\Models\VesselTurnAround;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Repositories\DatabaseRepository;

class HomeService
{
    protected $vesselInfoRepository, $vesselInfoService, $vesselTurnAroundService, $databaseRepository, $vesselService, $routeService;

    public function __construct(VesselInfoInterface $vesselInfoRepository, RouteService $routeService, DatabaseRepository $databaseRepository, VesselService $vesselService, VesselInfoService $vesselInfoService, VesselTurnAroundService $vesselTurnAroundService)
    {
        $this->vesselInfoRepository = $vesselInfoRepository;
        $this->databaseRepository = $databaseRepository;
        $this->vesselService = $vesselService;
        $this->routeService = $routeService;
        $this->vesselInfoService  = $vesselInfoService;
        $this->vesselTurnAroundService = $vesselTurnAroundService;
    }

    public function getData($request)
    {
        try {
            $years = (array) $request->input('years', []);
            $months = (array) $request->input('months', []);
            $gearTypes  = (array) $request->input('gear_types', []);
            $operators = (array) $request->input('operators', []);
            $ctnSizes = (array) $request->input('ctn_sizes', []);


            $sizeColumns = StaticDataHelper::containerSizes();

            if (!empty($ctnSizes)) {
                $sizeColumns = array_intersect($ctnSizes, $sizeColumns);
            }
            $selectColumns = array_merge(['id', 'vessel_info_id', 'type'], $sizeColumns);

            $data['operatorWiseHandling'] = $this->operatorWiseTueHandling($selectColumns, $years, $months, $gearTypes, $operators);
            $data['turnAroundData'] = $this->turnAroundData($years, $months, $gearTypes, $operators);
            $data['salesData'] = $this->salesData($years, $months, $ctnSizes);

            return $data;
        } catch (\Throwable $th) {
            \Log::error('Failed to fetch Dashboard Data', [
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function operatorWiseTueHandling($selectColumns, $years, $months, $gearTypes, $operators)
    {
        $operatorWiseData = ImportExportCount::select($selectColumns)
            ->with([
                'vesselInfos.route',
                'vesselInfos.vessel',
            ])
            ->whereHas('vesselInfos', function ($q) use ($years, $months, $operators) {
                if (!empty($years)) {
                    $q->whereIn(\DB::raw('YEAR(vessel_infos.date)'), $years);
                }

                if (!empty($months)) {
                    $q->whereIn(\DB::raw('MONTH(vessel_infos.date)'), $months);
                }

                if (!empty($operators)) {
                    $q->whereIn('operator', $operators);
                }
            })
            ->when(!empty($gearTypes), function ($q) use ($gearTypes) {
                $q->whereHas('vesselInfos.vessel', function ($qq) use ($gearTypes) {
                    $qq->whereIn('crane_status', $gearTypes);
                });
            })
            ->get()
            ->groupBy([
                'type',
                fn($item) => optional(optional($item->vesselInfos)->route)->name ?? 'N/A'
            ]);


        $operatorWiseTeusHandling = [];
        foreach ($operatorWiseData as $type => $routes) {
            foreach ($routes as $route => $data) {

                $operatorWiseTeusHandling[$type][$route]['laden'] = $data->sum('dc20') +
                    ($data->sum('dc40') * 2) +
                    ($data->sum('dc45') * 2) +
                    $data->sum('r20') +
                    ($data->sum('r40') * 2);

                $operatorWiseTeusHandling[$type][$route]['empty'] = $data->sum('mty20') +
                    ($data->sum('mty40') * 2);
            }
        }

        return $operatorWiseTeusHandling;
    }

    public function turnAroundData($years, $months, $gearTypes, $operators)
    {
        $filters = [];

        if (!empty($years)) {
            $filters['years'] = $years;
        }
        if (!empty($months)) {
            $filters['months'] = $months;
        }
        if (!empty($gearTypes)) {
            $filters['gear_types'] = $gearTypes;
        }
        if (!empty($operators)) {
            $filters['operators'] = $operators;
        }

        $turnAroundData = $this->vesselTurnAroundService->getAllVesselTurnArounds($filters);

        $berthStay = $turnAroundTime = $oaStay = 0;

        $turnAroundData->each(function ($v) use (&$berthStay, &$turnAroundTime, &$oaStay) {
            $oaStay += $v->oa_stay;
            $berthStay += $v->berth_stay;
            $turnAroundTime += $v->total_stay;
        });

        $totalCalls = count($turnAroundData);

        return [
            'totalVesselCall'     => $totalCalls,
            'avgAnchorageTime'    => $totalCalls ? round($oaStay / $totalCalls) : 0,
            'avgBerthTime'        => $totalCalls ? round($berthStay / $totalCalls) : 0,
            'avgTurnAroundTime'   => $totalCalls ? round($turnAroundTime / $totalCalls) : 0,
        ];
    }

    public function salesData($years, $months, $ctnSizes)
    {
        try {
            // 1. Map input sizes to DB column names
            $mappedSizes = collect($ctnSizes)->map(function ($size) {
                return match ($size) {
                    'dc20' => '20ft',
                    'r20'  => '20R',
                    'dc40' => '40ft',
                    'r40'  => '40R',
                    'dc45' => '45ft',
                    default => null,
                };
            })->filter()->values();

            $teuExpressions = $mappedSizes->map(function ($col) {
                $multiplier = in_array($col, ['40ft', '40R', '45ft']) ? 2 : 1;
                return "COALESCE(`{$col}`, 0) * {$multiplier}"; //if the column value is NULL, it returns 0.
            });

            // Fallback if no sizes selected
            if ($teuExpressions->isEmpty()) {
                $teuExpressions = collect([
                    "COALESCE(`20ft`, 0) + COALESCE(`40ft`, 0) * 2 + COALESCE(`45ft`, 0) * 2 + COALESCE(`20R`, 0) + COALESCE(`40R`, 0) * 2"
                ]);
            }

            $teuRawExpression = \DB::raw('SUM(' . $teuExpressions->implode(' + ') . ') as total_teu');

            // 3. Fetch sales data
            $salesData = ExportData::select([
                'commodity',
                'pod',
                'mlo',
                'date',
                'trade',
                $teuRawExpression
            ])
                ->when(!empty($years), fn($q) => $q->whereIn(\DB::raw('YEAR(date)'), $years))
                ->when(!empty($months), fn($q) => $q->whereIn(\DB::raw('MONTH(date)'), $months))
                ->groupBy('commodity', 'pod', 'mlo', 'trade', 'date')
                ->orderByDesc('total_teu')
                ->get();

            return $salesData;
        } catch (\Exception $e) {
            \Log::error('Failed to fetch export sales data: ' . $e->getMessage());
            return collect(); // safe fallback for frontend
        }
    }

    public function getAllUniqueOperators()
    {
        return $this->vesselInfoService->getAllUniqueOperators();
    }

    public function getUniqueYears()
    {
        return GetUniqueYearHelper::getUniqueYears([
            VesselInfos::class,
            ExportData::class,
        ]);
    }

    public function getAllMonths()
    {
        return StaticDataHelper::months();
    }

    public function getAllContainerSizes()
    {
        return StaticDataHelper::containerSizes();
    }
}
