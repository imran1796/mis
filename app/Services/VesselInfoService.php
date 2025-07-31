<?php

namespace App\Services;

use App\Models\MloWiseCount;
use App\Helpers\ContainerCountHelper;
use App\Interfaces\VesselInfoInterface;
use App\Models\VesselInfos;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Repositories\DatabaseRepository;

class VesselInfoService
{
    protected $vesselInfoRepository, $databaseRepository, $vesselService, $routeService;

    public function __construct(VesselInfoInterface $vesselInfoRepository, RouteService $routeService, DatabaseRepository $databaseRepository, VesselService $vesselService)
    {
        $this->vesselInfoRepository = $vesselInfoRepository;
        $this->databaseRepository = $databaseRepository;
        $this->vesselService = $vesselService;
        $this->routeService = $routeService;
    }

    public function getData($model, $where = null)
    {
        if ($where) {
            return $this->databaseRepository->getDataWhere($model, [], $where);
        } else {
            return $this->databaseRepository->getAllRecords($model, []);
        }
    }

    public function createVesselWiseData($data)
    {
        $rows = Excel::toArray([], $data['file'])[0] ?? [];

        if (count($rows) < 3) {
            Log::error('Invalid Excel Structure');
            return response()->json(['error' => 'Invalid Excel Structure'], 422);
        }

        $routeId = $data['route_id'];
        $date = Carbon::createFromFormat('d-M-Y', '01-' . $data['date'])->startOfMonth();
        $vesselInfoRecords = [];
        $importExportRecords = [];

        \DB::beginTransaction();
        try {
            foreach (array_slice($rows, 3) as $index => $row) {
                if (strtoupper(trim($row[0] ?? '')) === 'GRAND TOTAL') break;
                if (empty($row[0])) {
                    continue;
                }
                $vesselName = strtoupper(trim($row[1]));
                if (!$vesselName) {
                    \Log::error("Row {$index}: Vessel name is empty");
                    return response()->json(['error' => "Row {$index}: Vessel name is empty"], 422);
                }

                $vessel = $this->vesselService->getVesselByName($vesselName);
                if (!$vessel) {
                    \Log::error("Row {$index}: Vessel not found: $vesselName");
                    return response()->json(['error' => "Row {$index}: Vessel not found: $vesselName"], 422);
                }

                $requiredFields = [
                    'operator' => trim($row[5]) ?? '',
                    'rotation_no' => trim($row[29]) ?? '',
                    'arrival_date' => $row[2] ? Carbon::createFromFormat('d.m.y', $row[2])->format('Y-m-d') : '',
                    'berth_date' => $row[3] ? Carbon::createFromFormat('d.m.y', $row[3])->format('Y-m-d') : '',
                    'sail_date' => $row[4] ? Carbon::createFromFormat('d.m.y', $row[4])->format('Y-m-d') : '',
                    'jetty' => trim($row[32]) ?? '',
                    'local_agent' => trim($row[33]) ?? '',
                ];

                foreach ($requiredFields as $fieldName => $value) {
                    if (empty($value)) {
                        \Log::error("Missing $fieldName for vessel: $vesselName");
                        return response()->json(['error' => "Missing $fieldName for vessel: $vesselName"], 422);
                    }
                }

                $vesselInfoData = [
                    'vessel_id' => $vessel->id,
                    'route_id' => $routeId,
                    'rotation_no' => $requiredFields['rotation_no'],
                    'jetty' => $requiredFields['jetty'],
                    'operator' => $requiredFields['operator'],
                    'local_agent' => $requiredFields['local_agent'],
                    'effective_capacity' => !empty($row[30]) ? intval($row[30]) * 0.8 : $vessel->nominal_capacity * 0.8,
                    'arrival_date' => $requiredFields['arrival_date'],
                    'berth_date' => $requiredFields['berth_date'],
                    'sail_date' => $requiredFields['sail_date'],
                    'date' => $date,
                ];

                // $vesselInfoRecords[] = $vesselInfoData;
                $vesselInfo = $this->vesselInfoRepository->createVesselInfos($vesselInfoData);
                if (!$vesselInfo) {
                    \DB::rollBack();
                    \Log::error("Failed to insert vessel info");
                    return response()->json(['error' => "Failed to insert vessel info"], 500);
                }

                $importCols = array_slice($row, 9, 7);
                $exportCols = array_slice($row, 19, 7);
                $importData = $this->buildRow($vesselInfo->id, 'import', $importCols);
                $exportData = $this->buildRow($vesselInfo->id, 'export', $exportCols);

                $importSuccess = $this->vesselInfoRepository->createImportExportCount($importData);
                $exportSuccess = $this->vesselInfoRepository->createImportExportCount($exportData);

                if (!$importSuccess || !$exportSuccess) {
                    \DB::rollBack();
                    \Log::error("Failed to insert import/export counts");
                    return response()->json(['error' => "Failed to insert import/export counts"], 500);
                }
            }

            \DB::commit();
            return response()->json(['success' => 'Successfully Uploaded'], 200);
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Filed To Upload Vessel Wise Data: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    protected function buildRow(int $vesselInfoId, string $type, array $cols): array
    {
        return [
            'vessel_info_id' => $vesselInfoId,
            'type' => $type,
            'dc20' => $cols[0] ?? 0,
            'dc40' => $cols[1] ?? 0,
            'dc45' => $cols[2] ?? 0,
            'r20' => $cols[3] ?? 0,
            'r40' => $cols[4] ?? 0,
            'mty20' => $cols[5] ?? 0,
            'mty40' => $cols[6] ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function getAllVesselWiseData($filters)
    {
        if (empty($filters['from_date']) && empty($filters['to_date'])) {
            return [];
        }
        return $this->vesselInfoRepository->getAllVesselInfos($filters);
    }

    public function getAllUniqueOperators()
    {
        return $this->vesselInfoRepository->getAllUniqueOperators();
    }

    public function getAllRoutes()
    {
        return $this->routeService->getAllRoutes();
    }

    public function getDistinctVesselInfoDates($filters){
        return $this->vesselInfoRepository->getDistinctVEsselInfoDates($filters);
    }

    public function operatorWiseLifting($filters)
    {
        if (empty($filters['from_date']) && empty($filters['to_date']) && empty($filters['route_id'])) {
            return [];
        }

        $groupedByOperator = $this->vesselInfoRepository->getAllVesselInfos($filters)->groupBy('operator');

        $operators = $groupedByOperator->map(function ($vesselInfos) {
            $summary = [
                'operator' => $vesselInfos[0]->operator,
                'total_laden_import' => 0,
                'total_empty_import' => 0,
                'total_laden_export' => 0,
                'total_empty_export' => 0,
                'effective_capacity' => 0,
                'nominal_capacity' => 0,
                'vessel_calls' => $vesselInfos->count(),
                'unique_vessels' => $vesselInfos->pluck('vessel.vessel_name')->unique()->count(),
            ];

            foreach ($vesselInfos as $info) {
                $vessel = optional($info->vessel);
                $import = collect($info->importExportCounts)->firstWhere('type', 'import');
                $export = collect($info->importExportCounts)->firstWhere('type', 'export');

                $importTeu = ContainerCountHelper::calculateTeu($import);
                $exportTeu = ContainerCountHelper::calculateTeu($export);

                $summary['total_laden_import'] += $importTeu['laden'];
                $summary['total_empty_import'] += $importTeu['empty'];
                $summary['total_laden_export'] += $exportTeu['laden'];
                $summary['total_empty_export'] += $exportTeu['empty'];

                $summary['effective_capacity'] += (float) $info->effective_capacity;
                $summary['nominal_capacity'] += (float) $vessel->nominal_capacity;
            }

            return $summary;
        });

        // Grand totals
        $grandTotals = [
            'effective' => $operators->sum('effective_capacity'),
            'nominal' => $operators->sum('nominal_capacity'),
            'laden_import' => $operators->sum('total_laden_import'),
            'empty_import' => $operators->sum('total_empty_import'),
            'laden_export' => $operators->sum('total_laden_export'),
            'empty_export' => $operators->sum('total_empty_export'),
        ];

        $operators = $operators->map(function ($op) use ($grandTotals) {
            $effective = ContainerCountHelper::zeroIfEmpty($op['effective_capacity']);

            return array_merge($op, [
                'import_laden_eff' => ContainerCountHelper::percent($op['total_laden_import'], $effective),
                'import_empty_eff' => ContainerCountHelper::percent($op['total_empty_import'], $effective),
                'export_laden_eff' => ContainerCountHelper::percent($op['total_laden_export'], $effective),
                'export_empty_eff' => ContainerCountHelper::percent($op['total_empty_export'], $effective),

                'import' => ContainerCountHelper::percent(
                    $op['total_laden_import'] + $op['total_empty_import'],
                    $grandTotals['laden_import'] + $grandTotals['empty_import']
                ),
                'export_laden' => ContainerCountHelper::percent($op['total_laden_export'], $grandTotals['laden_export']),
                'export_empty' => ContainerCountHelper::percent($op['total_empty_export'], $grandTotals['empty_export']),
            ]);
        });

        $load_factor = [
            'imp_ldn_lf_eff' => ContainerCountHelper::percent($grandTotals['laden_import'], $grandTotals['effective']),
            'imp_ldn_lf_nom' => ContainerCountHelper::percent($grandTotals['laden_import'], $grandTotals['nominal']),
            'imp_mty_lf_eff' => ContainerCountHelper::percent($grandTotals['empty_import'], $grandTotals['effective']),
            'imp_mty_lf_nom' => ContainerCountHelper::percent($grandTotals['empty_import'], $grandTotals['nominal']),
            'exp_ldn_lf_eff' => ContainerCountHelper::percent($grandTotals['laden_export'], $grandTotals['effective']),
            'exp_ldn_lf_nom' => ContainerCountHelper::percent($grandTotals['laden_export'], $grandTotals['nominal']),
            'exp_mty_lf_eff' => ContainerCountHelper::percent($grandTotals['empty_export'], $grandTotals['effective']),
            'exp_mty_lf_nom' => ContainerCountHelper::percent($grandTotals['empty_export'], $grandTotals['nominal']),
        ];

        return [$operators, $load_factor];
    }

    public function socInOutBound($filters)
    {
        if (empty($filters)) {
            return [];
        }

        $types = ['20' => 'dc20', '40' => 'dc40', '45' => 'dc45', '20R' => 'r20', '40R' => 'r40', 'MTY20' => 'mty20', 'MTY40' => 'mty40'];
        $datas = [];
        $vessels = $this->vesselInfoRepository->getAllVesselInfos($filters)->groupBy('date');

        foreach ($vessels->sortKeys() as $month => $vslGroup) {
            foreach ($vslGroup as $v) {
                $monthKey = \Carbon\Carbon::parse($v->date)->format('M-y');

                $route = strtoupper($v->route->short_name);
                // dd($v->toArray());
                if (!isset($datas[$monthKey])) {
                    $datas[$monthKey] = [
                        'import' => array_fill_keys(array_keys($types), 0),
                        // 'importLdn' => $datas[$monthKey] + $datas[$monthKey]['import'];
                        'export' => array_fill_keys(array_keys($types), 0),
                        'calls' => ['SIN' => 0, 'CBO' => 0, 'CCU' => 0],
                        'vessels' => ['SIN' => [], 'CBO' => [], 'CCU' => []],
                        'vessels_count' => ['SIN' => 0, 'CBO' => 0, 'CCU' => 0],
                    ];
                }

                $datas[$monthKey]['calls'][$route]++;

                if (!in_array($v->vessel_id, $datas[$monthKey]['vessels'][$route])) {
                    $datas[$monthKey]['vessels'][$route][] = $v->vessel_id;
                    $datas[$monthKey]['vessels_count'][$route] += 1;
                }

                foreach ($v->importExportCounts as $iec) {
                    $direction = strtolower($iec->type);
                    foreach ($types as $key => $column) {
                        $datas[$monthKey][$direction][$key] += $iec->$column;
                    }
                }
            }
        }

        // dd($datas);

        return $datas;
    }

    public function vesselTurnAroundTime($filters)
    {
        if (empty($filters)) {
            return [];
        }

        $vessels = $this->vesselInfoRepository->getAllVesselInfos($filters);

        $results = $vessels->map(function ($v, $i) {
            // Default to midnight if time is null
            // $arrival = $v->arrival_date ? Carbon::parse($v->arrival_date . ' ' . ($v->arrival_time ?? '00:00:00')) : null;
            // $berth = $v->berth_date ? Carbon::parse($v->berth_date . ' ' . ($v->berth_time ?? '00:00:00')) : null;
            // $sail = $v->sail_date ? Carbon::parse($v->sail_date . ' ' . ($v->sail_time ?? '00:00:00')) : null;

            // $oaStay = ($arrival && $berth) ? $arrival->diffInHours($berth) : null;
            // $berthStay = ($berth && $sail) ? $berth->diffInHours($sail) : null;
            // $turnAroundTime = ($oaStay ?? 0) + ($berthStay ?? 0);

            $arrival = $v->arrival_date ? Carbon::parse($v->arrival_date . ' ' . ($v->arrival_time ?? '00:00:00')) : null;
            $berth = $v->berth_date ? Carbon::parse($v->berth_date . ' ' . ($v->berth_time ?? '00:00:00')) : null;
            $sail = $v->sail_date ? Carbon::parse($v->sail_date . ' ' . ($v->sail_time ?? '00:00:00')) : null;

            $oaStay = ($arrival && $berth) ? round($arrival->diffInHours($berth)) : null;
            $berthStay = ($berth && $sail) ? round($berth->diffInHours($sail)) : null;
            $turnAroundTime = ($oaStay ?? 0) + ($berthStay ?? 0);


            $import = $v->importExportCounts->firstWhere('type', 'import');
            $export = $v->importExportCounts->firstWhere('type', 'export');

            $importLaden = collect([$import->dc20 ?? 0, ($import->dc40 ?? 0) * 2, ($import->dc45 ?? 0) * 2, $import->r20 ?? 0, ($import->r40 ?? 0) * 2])->sum();
            $importEmpty = collect([$import->mty20 ?? 0, ($import->mty40 ?? 0) * 2])->sum();
            $importTotal = $importLaden + $importEmpty;

            $importBoxLaden = collect([$import->dc20 ?? 0, ($import->dc40 ?? 0), ($import->dc45 ?? 0), $import->r20 ?? 0, ($import->r40 ?? 0)])->sum();
            $importBoxEmpty = collect([$import->mty20 ?? 0, ($import->mty40 ?? 0)])->sum();
            $importBoxTotal = $importBoxLaden + $importBoxEmpty;

            $exportLaden = collect([$export->dc20 ?? 0, ($export->dc40 ?? 0) * 2, ($export->dc45 ?? 0) * 2, $export->r20 ?? 0, ($export->r40 ?? 0) * 2])->sum();
            $exportEmpty = collect([$export->mty20 ?? 0, ($export->mty40 ?? 0) * 2])->sum();
            $exportTotal = $exportLaden + $exportEmpty;

            $exportBoxLaden = collect([$export->dc20 ?? 0, ($export->dc40 ?? 0), ($export->dc45 ?? 0), $export->r20 ?? 0, ($export->r40 ?? 0)])->sum();
            $exportBoxEmpty = collect([$export->mty20 ?? 0, ($export->mty40 ?? 0)])->sum();
            $exportBoxTotal = $exportBoxLaden + $exportBoxEmpty;

            $totalCount = $importTotal + $exportTotal;
            $ttlMoves = $importBoxTotal + $exportBoxTotal;

            return [
                'sl' => $i + 1,
                'name' => $v->vessel->vessel_name ?? '-',
                'jetty' => $v->jetty ?? '-',
                'gear' => $v->vessel->crane_status ?? '-',
                'eta' => $arrival?->format('d M y H:i') ?? '-',
                'oa_stay' => $oaStay ?? '-',
                'berth_datetime' => $berth?->format('d M y H:i') ?? '-',
                'sail_datetime' => $sail?->format('d M y H:i') ?? '-',
                'berth_stay' => $berthStay ?? '-',
                'operator' => $v->operator ?? '-',
                'import_laden' => $importLaden,
                'import_empty' => $importEmpty,
                'import_total' => $importTotal,
                'export_laden' => $exportLaden,
                'export_empty' => $exportEmpty,
                'export_total' => $exportTotal,
                'ttl_moves' => $ttlMoves,
                'ttl_teus' => $totalCount,
                'turn_around_time' => $turnAroundTime,
            ];
        });


        return $results;
    }

    public function marketCompetitors($filters)
    {
        if (empty($filters)) {
            return [];
        }

        $types = [
            '20' => 'dc20',
            '40' => 'dc40',
            '45' => 'dc45',
            '20R' => 'r20',
            '40R' => 'r40',
            'MTY20' => 'mty20',
            'MTY40' => 'mty40',
        ];

        $marketCompetitors = $this->vesselInfoRepository->getAllVesselInfos($filters)->groupBy('operator');

        $results = [];

        // First calculate total import/export for % share
        $totalImport = 0;
        $totalExportLdn = 0;
        $totalExportMty = 0;

        foreach ($marketCompetitors as $operator => $vessels) {
            foreach ($vessels as $vessel) {
                foreach ($vessel->importExportCounts as $iec) {
                    // dd($iec);
                    if ($iec->type === 'import') {
                        // dd($iec->dc20);
                        $totalImport += $iec->dc20 + $iec->r20 + $iec->mty20 + (($iec->dc40 + $iec->dc45 + $iec->r40 + $iec->mty40) * 2);
                    } elseif ($iec->type === 'export') {
                        $totalExportLdn += $iec->dc20 + $iec->r20 + (($iec->dc40 + $iec->dc45 + $iec->r40) * 2);
                        $totalExportMty += $iec->mty20 + ($iec->mty40 * 2);
                    }
                }
            }
        }
        // dd($totalImport);

        foreach ($marketCompetitors as $operator => $vessels) {
            $importTeus = 0;
            $exportLdnTeus = 0;
            $exportMtyTeus = 0;
            $effectiveCap = 0;
            $callDates = [];

            $vesselIds = [];
            $localAgent = $vessels->first()->local_agent;

            foreach ($vessels as $vessel) {
                $effectiveCap += $vessel->effective_capacity;

                $vesselIds[$vessel->vessel_id] = true;

                foreach ($vessel->importExportCounts as $iec) {
                    if ($iec->type === 'import') {
                        $importTeus += $iec->dc20 + $iec->r20 + $iec->mty20 + (($iec->dc40 + $iec->dc45 + $iec->r40 + $iec->mty40) * 2);
                    } elseif ($iec->type === 'export') {
                        $exportLdnTeus += $iec->dc20 + $iec->r20 + (($iec->dc40 + $iec->dc45 + $iec->r40) * 2);
                        $exportMtyTeus += $iec->mty20 + ($iec->mty40 * 2);
                    }
                }
            }
            // $frequency = 'Fortnightly';
            // $frequency = for a operator one vessel's how many time a month repeated; 

            $results[$operator] = [
                'local_agent' => $localAgent,
                'numOfVsl' => count($vesselIds),
                'numOfCall' => count($vessels),
                'effectiveCapacity' => $effectiveCap,
                'effCapPerWeek' => round($effectiveCap / 4, 2),
                'slotPartner' => '',
                'slotBuyer' => '',
                'import%' => $totalImport > 0 ? round(($importTeus / $totalImport) * 100, 2) : 0,
                'exportLdn%' => $totalExportLdn > 0 ? round(($exportLdnTeus / $totalExportLdn) * 100, 2) : 0,
                'exportMty%' => $totalExportMty > 0 ? round(($exportMtyTeus / $totalExportMty) * 100, 2) : 0,
                'sailingFreq' => count($vesselIds) == count($vessels) ? 'Monthly' : 'Fortnightly',
                'vessels' => array_keys($vesselIds),
            ];
        }

        return $results;
    }

    public function vesselInfoReport($request)
    {
        $filters = $request->only([
            'from_date',
            'to_date',
            'route_id',
            'shipment_type',
            'ctn_size',
            'ctn_type',
            'report_type',
            'operator'
        ]);

        if (empty(array_filter($filters))) {
            return collect();
        }

        // Normalize dates
        if (!empty($filters['from_date'])) {
            $filters['from_date'] = Carbon::parse($filters['from_date'])->startOfMonth();
        }

        if (!empty($filters['to_date'])) {
            $filters['to_date'] = Carbon::parse($filters['to_date'])->endOfMonth();
        }

        $data = $this->vesselInfoRepository->getAllVesselInfos($filters);
        if ($data->isEmpty()) {
            return collect();
        }

        $grouped = match ($filters['report_type'] ?? 'operator-wise') {
            'operator-route-wise' => $data->groupBy(['operator', 'route.short_name']),
            'route-wise'          => $data->groupBy('route.short_name'),
            'operator-wise' => $data->groupBy('operator'),
            'vessel-wise' => $data->groupBy(['operator', 'vessel.vessel_name']),
            default => $data->groupBy('operator'),
        };

        $shipmentTypes = $filters['shipment_type'] ?? ['export', 'import'];
        $filteredCtnSizes = $filters['ctn_size'] ?? [];

        $hasRouteGrouping = $filters['report_type'] === 'operator-route-wise';
        $isVesselWise = $filters['report_type'] === 'vessel-wise';

        [$formatted, $totalTeus] = $this->formatOperatorSummary(
            $grouped,
            $shipmentTypes,
            $filteredCtnSizes,
            $hasRouteGrouping,
            $isVesselWise
        );

        uasort($formatted, function ($a, $b) use ($shipmentTypes) {
            $aTeu = $a[$shipmentTypes[0]]['teus'] ?? array_values($a)[0][$shipmentTypes[0]]['teus'];
            $bTeu = $b[$shipmentTypes[0]]['teus'] ?? array_values($b)[0][$shipmentTypes[0]]['teus'];
            return $bTeu <=> $aTeu;
        });
        // dd($formatted);

        return [$formatted, $totalTeus];
    }

    public function formatOperatorSummary(
        $data,
        array $shipmentTypes,
        array $filteredCtnSizes,
        bool $hasRouteGrouping = false,
        bool $isVesselWise = false
    ): array {
        $totalTeusByType = [
            'export' => ['empty' => 0, 'laden' => 0],
            'import' => ['empty' => 0, 'laden' => 0],
        ];

        $totalTeus = 0;

        // 1. Calculate total teus for % calculations
        foreach ($data as $operator => $group) {
            // $vessels = $hasRouteGrouping ? $group->flatten(1) : $group;
            if ($hasRouteGrouping) {
                $vessels =  $group->flatten(1);
            } else if ($isVesselWise) {
                $vessels =  $group->flatten(1);
            } else {
                $vessels = $group;
            }

            foreach ($vessels as $vessel) {
                foreach ($vessel->importExportCounts as $count) {
                    $type = $count['type'] ?? null;
                    if (!in_array($type, $shipmentTypes)) continue;

                    $teu = ContainerCountHelper::calculateTeu((object) $count);
                    $totalTeusByType[$type]['empty'] += $teu['empty'];
                    $totalTeusByType[$type]['laden'] += $teu['laden'];
                }
            }
        }

        // 2. Build summary
        $summaries = $data->mapWithKeys(function ($group, $operator) use (
            $hasRouteGrouping,
            $isVesselWise,
            $shipmentTypes,
            $filteredCtnSizes,
            $totalTeusByType,
            &$totalTeus
        ) {
            // $innerMap = $hasRouteGrouping ? $group : collect(['default' => $group]);
            if ($hasRouteGrouping) {
                $innerMap = $group;
            } else if ($isVesselWise) {
                $innerMap = $group;
            } else {
                $innerMap = collect(['default' => $group]);
            }

            // if($operator=='SKN'){
            // dd($operator,$innerMap->toArray());
            // }

            $routeSummary = $innerMap->mapWithKeys(function ($vsl, $routeKey) use (
                $shipmentTypes,
                $filteredCtnSizes,
                $totalTeusByType,
                &$totalTeus
            ) {
                $summary = [];
                foreach ($shipmentTypes as $type) {
                    $summary[$type] = $this->initializeSummary($filteredCtnSizes);
                }

                foreach ($vsl as $vessel) {
                    foreach ($vessel->importExportCounts as $count) {
                        $type = $count['type'] ?? null;
                        if (!in_array($type, $shipmentTypes)) continue;

                        foreach (array_keys($summary[$type]) as $key) {
                            if (isset($count[$key])) {
                                $summary[$type][$key] += (int) $count[$key];
                            }
                        }
                    }
                }

                // Final calculation for TEUs, units, and %
                foreach ($shipmentTypes as $type) {
                    $teuData = ContainerCountHelper::calculateTeu((object) $summary[$type]);
                    $boxData = ContainerCountHelper::calculateBox((object) $summary[$type]);

                    $summary[$type]['teus'] = $teuData['total'];
                    $summary[$type]['unit'] = $boxData['total'];
                    $totalTeus += $teuData['total'];

                    $ladenTeuCtn = array_sum([
                        $summary[$type]['dc20'] ?? 0,
                        ($summary[$type]['dc40'] ?? 0) * 2,
                        ($summary[$type]['dc45'] ?? 0) * 2,
                        $summary[$type]['r20'] ?? 0,
                        ($summary[$type]['r40'] ?? 0) * 2
                    ]);
                    $emptyTeuCtn = array_sum([
                        $summary[$type]['mty20'] ?? 0,
                        ($summary[$type]['mty40'] ?? 0) * 2
                    ]);

                    $totalTypeTeu = $totalTeusByType[$type]['laden'] + $totalTeusByType[$type]['empty'];

                    $summary[$type]['ladenTotal'] = $totalTypeTeu > 0
                        ? round(($ladenTeuCtn / $totalTypeTeu) * 100, 2) : 0;
                    $summary[$type]['ladenLaden'] = $totalTeusByType[$type]['laden'] > 0
                        ? round(($ladenTeuCtn / $totalTeusByType[$type]['laden']) * 100, 2) : 0;
                    $summary[$type]['emptyTotal'] = $totalTypeTeu > 0
                        ? round(($emptyTeuCtn / $totalTypeTeu) * 100, 2) : 0;
                    $summary[$type]['emptyEmpty'] = $totalTeusByType[$type]['empty'] > 0
                        ? round(($emptyTeuCtn / $totalTeusByType[$type]['empty']) * 100, 2) : 0;
                    $summary[$type]['ttlExisting'] = $totalTypeTeu > 0
                        ? round((($emptyTeuCtn + $ladenTeuCtn) / $totalTypeTeu) * 100, 2) : 0;
                }

                return [$routeKey => $summary];
            });

            return [
                $operator => ($hasRouteGrouping || $isVesselWise)
                    ? $routeSummary
                    : $routeSummary->first()
            ];
        });

        return [$summaries->toArray(), $totalTeus];
    }




    private function initializeSummary(array $filteredCtnSizes): array
    {
        $defaults = ['dc20', 'dc40', 'dc45', 'r20', 'r40', 'mty20', 'mty40'];
        $sizes = array_filter($defaults, fn($sz) => empty($filteredCtnSizes) || in_array($sz, $filteredCtnSizes));
        $summary = [];

        foreach ($sizes as $sz) {
            $summary[$sz] = 0;
        }

        return array_merge($summary, [
            'unit' => 0,
            'teus' => 0,
            'teus_p' => 0,
            'ladenTotal' => 0,
            'ladenLaden' => 0,
            'emptyTotal' => 0,
            'emptyEmpty' => 0,
            'ttlExisting' => 0
        ]);
    }

    public function updateVesselInfoTime(array $data)
    {
        return $this->vesselInfoRepository->updateVesselInfoTime($data);
    }

    public function operatorWiseContainerHandling($request){
        $filters= [
            'from_date' => Carbon::parse($request['from_date'])->format('M-Y'),
            'to_date' => Carbon::parse($request['to_date'])->format('M-Y'),
            'route_id' => $request->route_id
        ];

        $data = $this->vesselInfoRepository->getAllVesselInfos($filters)->sortBy([
            ['operator', 'asc'],
            ['sail_date', 'asc'],
        ])->values();

        return $data;
    }

    public function deletVesselInfoByDateRoute($filters){
        return $this->vesselInfoRepository->deletVesselInfoByDateRoute($filters);
    }
}
