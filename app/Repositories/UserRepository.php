<?php

namespace App\Repositories;

use App\Interfaces\UserInterface;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserRepository implements UserInterface
{
    public function getAllUsers()
    {
        return User::all();
    }

    public function getUserById($id)
    {
        return User::findOrFail($id);
    }

    public function createUser(array $data)
    {
    \DB::beginTransaction();
        try {
            $user = User::create($data);
            \DB::commit();
            return response()->json(['success' => 'Successfully Created User', 'User' => $user], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Creating User: ' . $e->getMessage());
            return response()->json(['error' => "Error Creating User: " . $e->getMessage()], 500);
        }
    }

    public function updateUser($id, array $data)
    {

        \DB::beginTransaction();
        try {
            $UserUpdate = User::findOrFail($id);
            $UserUpdate->update($data);
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Updated User',
                'User' => $UserUpdate
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Updating User: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Updating User: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        \DB::beginTransaction();
        try {
            $User = User::findOrFail($id);
            $User->delete();
            \DB::commit();
            return response()->json([
                'success' => 'Successfully Deleted User'
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error Deleting User: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error Deleting User: ' . $e->getMessage()
            ], 500);
        }
    }
}
