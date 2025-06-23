<?php

namespace App\Services;

use App\Interfaces\VesselTurnAroundInterface;
use App\Models\VesselTurnAround;
use App\Repositories\VesselTurnAroundRepository;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class VesselTurnAroundService
{
    protected $vesselTurnAroundRepository, $vesselInfoService;

    public function __construct(VesselTurnAroundInterface $vesselTurnAroundRepository, VesselInfoService $vesselInfoService)
    {
        $this->vesselTurnAroundRepository = $vesselTurnAroundRepository;
        $this->vesselInfoService = $vesselInfoService;
    }

    public function getAllVesselTurnArounds($filters)
    {
        if (empty($filters['from_date']) && empty($filters['to_date'])) {
            return [];
        }
        return $this->vesselTurnAroundRepository->getAllVesselTurnArounds($filters);
    }

    public function getVesselTurnAroundById($id)
    {
        return $this->vesselTurnAroundRepository->getVesselTurnAroundById($id);
    }

    public function getDistinctVesselTurnAroundDates($filters){
        return $this->vesselTurnAroundRepository->getDistinctVesselTurnAroundDates($filters);
    }

    public function createVesselTurnAround(array $data)
    {
        $rows = Excel::toArray([], $data['file'])[0] ?? [];
        $date = Carbon::createFromFormat('M-Y', $data['date'])->startOfMonth();
        $dataRows = array_slice($rows, 3);

        foreach ($dataRows as $index => $row) {
            if (empty($row[1]) || empty($row[2]) || empty($row[3])) {
                continue;
            }

            try {
                $vesselName = trim($row[1]);
                $jetty = trim($row[2]);
                $craneStatusRaw = strtolower(trim($row[3]));
                $craneStatus = $craneStatusRaw === 'gearless' ? 'GL' : 'G';
                $operator = trim($row[9]);

                $eta = $this->parseExcelDate($row[4]);
                $berthTime = $this->parseExcelDate($row[6]);
                $sailTime = $this->parseExcelDate($row[7]);

                $this->vesselInfoService->updateVesselInfoTime([
                    'vessel_name' => $vesselName,
                    'date' => $date,
                    'eta' => $eta,
                    'berth_time' => $berthTime,
                    'sail_time' => $sailTime,
                ]);                
                
                $oaStay = round(floatval($row[5]));
                $berthStay = round(floatval($row[8]));
                $ttlStay = round(floatval($row[18] ?? 0));

                $import_ldn_teu = intval($row[10] ?? 0);
                $import_mty_teu = intval($row[11] ?? 0);
                $export_ldn_teu = intval($row[13] ?? 0);
                $export_mty_teu = intval($row[14] ?? 0);

                $total_box = intval($row[16] ?? 0);
                $total_teu = intval($row[17] ?? 0);

                VesselTurnAround::create(
                    [
                        'vessel_name' => $vesselName,
                        'jetty' => $jetty,
                        'crane_status' => $craneStatus,
                        'eta' => $eta,
                        'berth_time' => $berthTime,
                        'sail_time' => $sailTime,
                        'oa_stay' => $oaStay,
                        'berth_stay' => $berthStay,
                        'total_stay' => $ttlStay,
                        'operator' => $operator,
                        'import_ldn_teu' => $import_ldn_teu,
                        'import_mty_teu' => $import_mty_teu,
                        'export_ldn_teu' => $export_ldn_teu,
                        'export_mty_teu' => $export_mty_teu,
                        'total_box' => $total_box,
                        'total_teu' => $total_teu,
                        'date' => $date,
                    ]
                );
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
        }

        return response()->json(['success' => 'Vessel Turnaround data uploaded successfully.']);
    }

    private function parseExcelDate($excelValue)
    {
        if (is_numeric($excelValue)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelValue));
        }

        return Carbon::parse($excelValue);
    }

    public function updateVesselTurnAround($id, array $data)
    {
        return $this->vesselTurnAroundRepository->updateVesselTurnAround($id, $data);
    }

    public function deleteVesselTurnAround($id)
    {
        return $this->vesselTurnAroundRepository->deleteVesselTurnAround($id);
    }
}
