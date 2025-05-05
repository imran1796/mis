<?php

namespace App\Repositories;

use App\Interfaces\VesselInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Vessel;

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
}
