<?php

namespace App\Services;

use App\Imports\MyImport;
use App\Interfaces\MloInterface;
use App\Models\MloWiseCount;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use App\Repositories\DatabaseRepository;

class MloService
{
    protected $mloRepository, $routeService, $databaseRepository;

    public function __construct(MloInterface $mloRepository, RouteService $routeService,  DatabaseRepository $databaseRepository)
    {
        $this->mloRepository = $mloRepository;
        $this->routeService = $routeService;
        $this->databaseRepository = $databaseRepository;
    }

    public function getData($model, $where = null)
    {
        if ($where) {
            return $this->databaseRepository->getDataWhere($model, [], $where);
        } else {
            return $this->databaseRepository->getAllRecords($model, []);
        }
    }

    public function getAllMlos()
    {
        return $this->mloRepository->getAllMlos();
    }

    public function getAllRoutes()
    {
        return $this->routeService->getAllRoutes();
    }

    public function getAllMloWiseCount($filters)
    {
        return $this->mloRepository->getAllMloWiseCount($filters);
    }

    public function getMloById($id)
    {
        return $this->mloRepository->getMloById($id);
    }

    // public function createMlo(UploadedFile $file): void
    // {
    //     $rows = Excel::toArray(new MyImport, $file)[0];

    //     $filteredData = [];
    //     foreach ($rows as $index => $row) {
    //         if (empty($row[2])) {
    //             continue;
    //         }

    //         $filteredData[] = [
    //             'line_belongs_to'        => trim(strtoupper($row[1])),
    //             'mlo_code'     => trim(strtoupper($row[0])),
    //             'mlo_details'      => trim(strtoupper($row[2])),
    //         ];
    //     }

    //     $this->mloRepository->createMlo($filteredData);
    // }

    public function createMlo(array $data)
    {
        $mlo = $this->mloRepository->getMloByCode($data['mlo_code']);
        if ($mlo) {
            $mlo['effective_to'] = now()->subDay();
            $this->mloRepository->updateMlo($mlo->id, $mlo->toArray());
        }
        return $this->mloRepository->createMlo($data);
    }

    public function updateMlo($id, array $data)
    {
        return $this->mloRepository->updateMlo($id, $data);
    }

    public function deleteMlo($id)
    {
        return $this->mloRepository->deleteMlo($id);
    }

    public function createMloWiseCount($data)
    {
        $rows = Excel::toArray([], $data['file'])[0] ?? [];

        if (count($rows) < 3) {
            \Log::error('Invalid Excel Structure');
            return response()->json(['error' => "Invalid Excel Structure"], 500);
        }

        $route = $data['route_id'];
        // $date = Carbon::createFromFormat('M-Y', $data['date'])->startOfMonth();
        $date = Carbon::createFromFormat('d-M-Y', '01-' . $data['date'])->startOfMonth();
        // dd($data['date'],$date);
        $records = [];

        foreach (array_slice($rows, 2) as $index => $row) {
            $mloCode = strtoupper(trim($row[0] ?? ''));
            if (!$mloCode) {
                \Log::error('Missing MLO code');
                return response()->json(['error' => "Missing Mlo Code. Row: " . $index + 3], 500);
            }

            //non letter char remove
            $normalizedCode = strtolower(preg_replace('/[^a-z]/i', '', trim($mloCode)));
            if ($normalizedCode === 'gtotal' || $normalizedCode === 'grandtotal') {
                break;
            }

            $records[] = $this->buildRow($route, $date, $mloCode, 'import', array_slice($row, 1, 7));
            $records[] = $this->buildRow($route, $date, $mloCode, 'export', array_slice($row, 11, 7));
        }

        return $this->mloRepository->createMloWiseCount($records);
    }

    public function deleteMloWiseCountByDateRoute($request)
    {
        try {
            \DB::beginTransaction();

            MloWiseCount::whereDate('date', $request->date)
                ->where('route_id', $request->route_id)
                ->delete();

            \DB::commit();

            return response()->json([
                'success' => 'Data deleted successfully for ' . Carbon::parse($request->date)->format('F Y')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MloWiseCount Deletion Failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Failed to delete data. Please try again later.'
            ], 500);
        }
    }

    protected function buildRow(string $route, string $date, string $mlo, string $type, array $cols): array
    {
        return [
            'route_id' => $route,
            'date' => $date,
            'mlo_code' => $mlo,
            'type' => $type,
            'dc20' => $cols[0] ?? 0,
            'dc40' => $cols[1] ?? 0,
            'dc45' => $cols[2] ?? 0,
            'r20'  => $cols[3] ?? 0,
            'r40'  => $cols[4] ?? 0,
            'mty20' => $cols[5] ?? 0,
            'mty40' => $cols[6] ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function mloWiseSummary($filters)
    {
        $mloWiseDatas = $this->mloRepository->getAllMloWiseCount($filters)->groupBy(['mlo_code', 'date']);

        $results = [];
        foreach ($mloWiseDatas as $mlo => $mloWiseData) {

            $results[$mlo] = [
                'totalImportLdnTeus' => 0,
                'totalImportMtyTeus' => 0,
                'totalExportLdnTeus' => 0,
                'totalExportMtyTeus' => 0,
                'mlo' => $mloWiseData->first()->first()->mlo->mlo_code ?? '',
                'lineBelongsTo' => $mloWiseData->first()->first()->mlo->line_belongs_to ?? '',
                'mloDetails' => $mloWiseData->first()->first()->mlo->mlo_details ?? '',
                'totalMonth' => count($mloWiseData),
            ];

            foreach ($mloWiseData as $date => $datas) {
                $month = Carbon::parse($date)->format('M');

                if (!isset($results[$mlo]['permonth'][$month])) {
                    $results[$mlo]['permonth'][$month] = [
                        'importLdnTeus' => 0,
                        'importMtyTeus' => 0,
                        'exportLdnTeus' => 0,
                        'exportMtyTeus' => 0,
                    ];
                }
                foreach ($datas as $data) {
                    $ldnTeu = ($data->dc20 ?? 0) + ($data->r20 ?? 0) + ((($data->dc40 ?? 0) + ($data->dc45 ?? 0) + ($data->r40 ?? 0)) * 2);
                    $mtyTeu = ($data->mty20 ?? 0) + (($data->mty40 ?? 0) * 2);
                    if ($data->type == 'import') {
                        // $importLaden = ($data->dc20 ?? 0) + ($data->r20 ?? 0) + ((($data->dc40 ?? 0) + ($data->dc45 ?? 0) + ($data->r40 ?? 0)) * 2);
                        // $importEmpty = ($data->mty20 ?? 0) + (($data->mty40 ?? 0) * 2);
                        $results[$mlo]['permonth'][$month]['importLdnTeus'] += $ldnTeu;
                        $results[$mlo]['permonth'][$month]['importMtyTeus'] += $mtyTeu;
                        $results[$mlo]['totalImportLdnTeus'] += $ldnTeu;
                        $results[$mlo]['totalImportMtyTeus'] += $mtyTeu;
                    } else {
                        // $exportLaden = ($data->dc20 ?? 0) + ($data->r20 ?? 0) + ((($data->dc40 ?? 0) + ($data->dc45 ?? 0) + ($data->r40 ?? 0)) * 2);
                        // $exportEmpty = ($data->mty20 ?? 0) + (($data->mty40 ?? 0) * 2);
                        $results[$mlo]['permonth'][$month]['exportLdnTeus'] += $ldnTeu;
                        $results[$mlo]['permonth'][$month]['exportMtyTeus'] += $mtyTeu;
                        $results[$mlo]['totalExportLdnTeus'] += $ldnTeu;
                        $results[$mlo]['totalExportMtyTeus'] += $mtyTeu;
                    }
                }
            }
        }
        return $results;
    }

    public function socOutboundMarketStrategy(array $filters): array
    {
        if (empty($filters)) {
            return [];
        }

        $query = MloWiseCount::with('mlo');

        if (!empty($filters['route_id'])) {
            $query->whereIn('route_id', (array) $filters['route_id']);
        }

        if (!empty($filters['from_date'])) {
            $fromDate = Carbon::parse($filters['from_date'])->startOfMonth();
            $query->whereDate('date', '>=', $fromDate);
        }

        if (!empty($filters['to_date'])) {
            $toDate = Carbon::parse($filters['to_date'])->endOfMonth();
            $query->whereDate('date', '<=', $toDate);
        }

        $mlos = $query->get();

        // 🔁 Group by MLO Line → then by MLO Code
        $grouped = $mlos
            ->groupBy(function ($item) {
                return optional($item->mlo)->line_belongs_to ?? $item->mlo_code;
            })
            ->map(function ($groupedByLine) {
                return $groupedByLine->groupBy('mlo_code');
            });

        $summary = [];

        foreach ($grouped as $line => $mloGroup) {
            $mloCodes = $mloGroup->keys()->toArray();
            $firstRecord = $mloGroup->first()->first(); // collection of collections

            $mloName = optional($firstRecord->mlo)->mlo_details ?? $firstRecord->mlo_code;

            $exportLdn = 0;
            $exportMty = 0;

            foreach ($mloGroup as $records) {
                foreach ($records as $record) {
                    if ($record->type === 'export') {
                        $exportLdn +=
                            ($record->dc20 ?? 0) +
                            ($record->r20 ?? 0) +
                            2 * (
                                ($record->dc40 ?? 0) +
                                ($record->dc45 ?? 0) +
                                ($record->r40 ?? 0)
                            );

                        $exportMty +=
                            ($record->mty20 ?? 0) +
                            2 * ($record->mty40 ?? 0);
                    }
                }
            }

            $summary[$line] = [
                'mlo_code'  => implode('/', $mloCodes),
                'mlo_name'  => $mloName,
                'exportLdn' => (int) round($exportLdn / 4),
                'exportMty' => (int) round($exportMty / 4),
            ];
        }

        return $summary;
    }

    public function mloWiseContainerHandling($request){
        $filters= [
            'from_date' => Carbon::parse($request['date'])->format('M-Y'),
            'to_date' => Carbon::parse($request['date'])->format('M-Y'),
            'route_id' => [$request->route_id]
        ];

        $data = $this->mloRepository->getAllMloWiseCount($filters)->groupBy('mlo_code')->sortKeys();
        $range = Carbon::parse($request['date'])->format('M-y');
        $routeNames = [1 => 'SIN', 2 => 'CBO', 3 => 'CCU'];
        $route = collect($filters['route_id'] ?? [])
            ->map(fn($id) => $routeNames[$id] ?? '')
            ->filter()
            ->implode(', ');
        $fileName = "MLO_Wise_Container_Handling - {$range}" . ($route ? " - {$route}" : '') . ".xlsx";

        return [$data,$route,$range,$fileName];

    }
}
