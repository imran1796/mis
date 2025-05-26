<?php

namespace App\Repositories;

use App\Interfaces\VesselTurnAroundInterface;
use Illuminate\Support\Facades\Log;
use App\Models\VesselTurnAround;
use Carbon\Carbon;

class VesselTurnAroundRepository implements VesselTurnAroundInterface
{
    public function getAllVesselTurnArounds($filters = [])
    {
        $query = VesselTurnAround::query();

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


    public function getVesselTurnAroundById($id)
    {
        return VesselTurnAround::findOrFail($id);
    }

    public function createVesselTurnAround(array $data)
    {
        \DB::beginTransaction();
        try {
            $vesselTurnAroundStore = VesselTurnAround::create($data);
            \DB::commit();
            return response()->json(['success' => 'Successfully Created VesselTurnAround', 'VesselTurnAround' => VesselTurnAroundStore], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Creating VesselTurnAround: ' . $e->getMessage());
            return response()->json(['error' => "Error Creating VesselTurnAround: " . $e->getMessage()], 500);
        }
    }

    public function updateVesselTurnAround($id, array $data)
    {

        \DB::beginTransaction();
        try {
            $vesselTurnAroundUpdate = VesselTurnAround::findOrFail($id);
            $vesselTurnAroundUpdate->update($data);
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Updated VesselTurnAround',
                'VesselTurnAround' => $vesselTurnAroundUpdate
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Updating VesselTurnAround: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Updating VesselTurnAround: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteVesselTurnAround($id)
    {
        \DB::beginTransaction();
        try {
            $vesselTurnAround = VesselTurnAround::findOrFail($id);
            $vesselTurnAround->delete();
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Deleted VesselTurnAround'
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Deleting VesselTurnAround: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Deleting VesselTurnAround: ' . $e->getMessage()
            ], 500);
        }
    }
}
