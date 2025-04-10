<?php

namespace App\Repositories;

use App\Interfaces\FeederInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Feeder;

class FeederRepository implements FeederInterface
{
    public function getAllFeeders()
    {
        return Feeder::all();
    }

    public function getFeederById($id)
    {
        return Feeder::findOrFail($id);
    }

    public function createFeeder(array $data)
    {
    \DB::beginTransaction();
        try {
            FeederStore = Feeder::create($data);
            \DB::commit();
            return response()->json(['success' => 'Successfully Created Feeder', 'Feeder' => FeederStore], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Creating Feeder: ' . $e->getMessage());
            return response()->json(['error' => "Error Creating Feeder: " . $e->getMessage()], 500);
        }
    }

    public function updateFeeder($id, array $data)
    {

        \DB::beginTransaction();
        try {
            $FeederUpdate = Feeder::findOrFail($id);
            $FeederUpdate->update($data);
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Updated Feeder',
                'Feeder' => $FeederUpdate
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Updating Feeder: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Updating Feeder: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteFeeder($id)
    {
        \DB::beginTransaction();
        try {
            $Feeder = Feeder::findOrFail($id);
            $Feeder->delete();
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Deleted Feeder'
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Deleting Feeder: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Deleting Feeder: ' . $e->getMessage()
            ], 500);
        }
    }
}
