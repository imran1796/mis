<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class VesselJsonToDBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('data/vessels.json');

        if (!File::exists($filePath)) {
            Log::error("The file $filePath does not exist.");
            return;
        }

        $json = File::get($filePath);

        $vessels = json_decode($json, true);

        foreach ($vessels as $vessel) {
            DB::table('vessels')->updateOrInsert(
                [
                    'vessel_name' => $vessel['vessel_name'],
                    'imo_no' => $vessel['imo_no'],
                ],
                [
                    'length_overall' => $vessel['loa'],
                    'crane_status' => $vessel['crane_status'],
                    'nominal_capacity' => $vessel['nominal_capacity'],
                ]
            );
        }
    }
}
