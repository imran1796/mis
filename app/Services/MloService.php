<?php

namespace App\Services;

use App\Imports\MyImport;
use App\Interfaces\MloInterface;
use App\Models\MloWiseCount;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;

class MloService
{
    protected $mloRepository,$routeService;

    public function __construct(MloInterface $mloRepository,RouteService $routeService)
    {
        $this->mloRepository = $mloRepository;
        $this->routeService = $routeService;
    }

    public function getAllMlos()
    {
        return $this->mloRepository->getAllMlos();
    }

    public function getAllRoutes(){
        return $this->routeService->getAllRoutes();
    }

    public function getAllMloWiseCount($filters){
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
        $date = Carbon::createFromFormat('M-Y', $data['date'])->startOfMonth();
        $records = [];

        foreach (array_slice($rows, 2) as $index => $row) {
            $mloCode = strtoupper(trim($row[0] ?? ''));
            if (!$mloCode) {
                \Log::error('Missing MLO code');
                return response()->json(['error' => "Missing Mlo Code. Row: " . $index + 3], 500);
            }
            if (strtoupper($mloCode) === 'G.TOTAL') {
                break;
            }

            $records[] = $this->buildRow($route, $date, $mloCode, 'import', array_slice($row, 1, 7));
            $records[] = $this->buildRow($route, $date, $mloCode, 'export', array_slice($row, 11, 7));
        }
        
        return $this->mloRepository->createMloWiseCount($records);
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

    public function mloWiseSummary($filters){
        // $data = $this->mloRepository->getAllMloWiseCount($filters);
        // $data = MloWiseCount::get()->groupBy('mlo_code');
        // dd($data[0],$data[1]);

    }
}
