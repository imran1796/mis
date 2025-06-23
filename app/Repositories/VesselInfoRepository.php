<?php

namespace App\Repositories;

use App\Interfaces\VesselInfoInterface;
use Illuminate\Support\Facades\Log;
use App\Models\ImportExportCount;
use App\Models\VesselInfos;
use Carbon\Carbon;

class VesselInfoRepository implements VesselInfoInterface
{
    public function getAllVesselInfos($filters = [])
    {
        $query = VesselInfos::with([
            'vessel',
            'route',
            'importExportCounts'
            // => function ($q) use ($filters) {
            //     $baseColumns = ['id', 'vessel_info_id', 'type'];

            //     if (!empty($filters['ctn_size']) && is_array($filters['ctn_size'])) {
            //         $columns = array_merge($baseColumns, $filters['ctn_size']);
            //         $q->select($columns);
            //     } else {
            //         $q->select($baseColumns);
            //     }

            //     if (!empty($filters['shipment_type']) && is_array($filters['shipment_type'])) {
            //         $q->whereIn('type', $filters['shipment_type']);
            //     }
            // }
        ]);

        if (!empty($filters['route_id'])) {
            $query->whereIn('route_id', $filters['route_id']);
        }

        if (!empty($filters['from_date'])) {
            $fromDate = Carbon::parse($filters['from_date'])->startOfMonth();
            $query->whereDate('date', '>=', $fromDate);
        }

        if (!empty($filters['to_date'])) {
            $toDate = Carbon::parse($filters['to_date'])->endOfMonth();
            $query->whereDate('date', '<=', $toDate);
        }

        if (!empty($filters['operator'])) {
            $query->whereIn('operator', $filters['operator']);
        }

        return $query->get();
    }

    public function getAllImportExportCount($filters = [])
    {
        if (empty($filters)) {
            return collect();
        }

        $query = ImportExportCount::query();
        if (!empty($filters['from_date'])) {
            $query->whereDate('date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('date', '<=', $filters['to_date']);
        }

        if (!empty($filters['vessel_name'])) {
            $query->whereIn('mlo_code', $filters['mlo']);
        }

        if (!empty($filters['pod'])) {
            $query->whereIn('route_id', $filters['pod']);
        }

        if (!empty($filters['type'])) {
            if ($filters['type'] == 'all') {
                $query->whereIn('type', ['IMPORT', 'EXPORT']);
            } else {
                $query->where('type', $filters['type']);
            }
        }

        return $query->get();
    }

    public function getAllUniqueOperators()
    {
        return VesselInfos::query()
            ->select('operator')
            ->distinct()
            ->pluck('operator');
    }

    public function createVesselInfos(array $data)
    {
        return VesselInfos::create($data);
    }

    public function createImportExportCount(array $data)
    {
        return ImportExportCount::create($data);
    }

    public function updateVesselInfoTime(array $data)
    {
        try {
            $vesselName = trim(strtoupper($data['vessel_name']));
            VesselInfos::whereDate('date', Carbon::parse($data['date'])->toDateString())
                ->where('arrival_date', Carbon::parse($data['eta'])->toDateString())
                ->whereHas('vessel', function ($query) use ($vesselName) {
                    $query->whereRaw('TRIM(UPPER(vessel_name)) = ?', [$vesselName]);
                })
                ->update([
                    'arrival_time' => Carbon::parse($data['eta'])->format('H:i:s'),
                    'berth_time' => Carbon::parse($data['berth_time'])->format('H:i:s'),
                    'sail_time' => Carbon::parse($data['sail_time'])->format('H:i:s'),
                ]);
        } catch (\Throwable $th) {
            Log::error('Failed to update vessel times', [
                'error' => $th->getMessage(),
                'data' => $data,
            ]);
        }
    }

    public function deletVesselInfoByDateRoute($filters = [])
    {
        \DB::beginTransaction();
        try {
            // Get all vessel_info records for the route and date
            $vesselInfos = VesselInfos::where('route_id', $filters['route_id'])
                ->whereDate('date', $filters['date'])
                ->with('importExportCounts')
                ->get();

            if ($vesselInfos->isEmpty()) {
                return response()->json(['error' => 'No records found for the selected route and date'], 404);
            }

            foreach ($vesselInfos as $vesselInfo) {
                $vesselInfo->importExportCounts()->delete();
                $vesselInfo->delete();
            }

            \DB::commit();
            return response()->json(['success' => 'Vessel-wise data deleted successfully.'], 200);
        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Failed to delete vessel-wise data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'route_id' => $filters['route_id'],
                'date' => $filters['date']
            ]);
            return response()->json(['error' => 'An unexpected error occurred while deleting.'], 500);
        }
    }

    public function getDistinctVesselInfoDates($filters = [])
    {
        return VesselInfos::select('date', 'route_id')
            ->with('route', 'importExportCounts')
            ->distinct('date')
            ->orderByDesc('date')
            ->when(!empty($filters['route_id']), function ($q) use ($filters) {
                $q->whereIn('route_id', $filters['route_id']);
            })
            ->when(!empty($filters['from_date']), function ($q) use ($filters) {
                $q->whereDate('date', '>=', Carbon::parse($filters['from_date'])->startOfMonth());
            })
            ->when(!empty($filters['to_date']), function ($q) use ($filters) {
                $q->whereDate('date', '<=', Carbon::parse($filters['to_date'])->startOfMonth());
            })
            ->get()
            ->groupBy(['date', 'route_id']);
    }
}
