<?php

namespace App\Repositories;

use App\Interfaces\VesselInterface;
use App\Models\ImportExportCount;
use Illuminate\Support\Facades\Log;
use App\Models\Vessel;
use App\Models\VesselInfos;
use Carbon\Carbon;

class VesselRepository implements VesselInterface
{
    public function getAllVessels()
    {
        return Vessel::all();
    }

    public function getVesselById($id)
    {
        return Vessel::findOrFail($id);
    }

    public function getVesselByName(string $name)
    {
        return Vessel::where('vessel_name', $name)->first();
    }

    public function createVessel(array $data)
    {
        \DB::beginTransaction();
        try {
            // foreach ($data as $vessel) {
            Vessel::updateOrCreate(
                [
                    'vessel_name' => $data['vessel_name'],
                    'imo_no'     => $data['imo_no'],
                ],
                [
                    'length_overall'     => $data['length_overall'],
                    'crane_status'      => $data['crane_status'],
                    'nominal_capacity'   => $data['nominal_capacity'],
                ]
            );
            // }
            \DB::commit();
            return response()->json(['success' => 'Successfully Created Vessel'], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Creating Vessel: ' . $e->getMessage());
            return response()->json(['error' => "Error Creating Vessel: " . $e->getMessage()], 500);
        }
    }

    public function updateVessel($id, array $data)
    {

        \DB::beginTransaction();
        try {
            $VesselUpdate = Vessel::findOrFail($id);
            $VesselUpdate->update($data);
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Updated Vessel',
                'Vessel' => $VesselUpdate
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Updating Vessel: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Updating Vessel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteVessel($id)
    {
        \DB::beginTransaction();
        try {
            $Vessel = Vessel::findOrFail($id);
            $Vessel->delete();
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Deleted Vessel'
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Deleting Vessel: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Deleting Vessel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createVesselInfos(array $data)
    {
        return VesselInfos::create($data);
    }

    public function createImportExportCount(array $data)
    {
        return ImportExportCount::create($data);
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

    public function getAllVesselInfos($filters = [])
    {
        $query = VesselInfos::with([
            'importExportCounts',
            'vessel'
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

        return $query->get();
    }
}
