<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MLOJsonToDBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('data/mlos.json');

        if (!File::exists($filePath)) {
            Log::error("The file $filePath does not exist.");
            return;
        }

        $json = File::get($filePath);

        $mlos = json_decode($json, true);

        foreach ($mlos as $mlo) {
            DB::table('mlos')->updateOrInsert(
                [
                    'line_belongs_to' => $mlo['line_belongs_to'],
                    'mlo_code'        => $mlo['mlo_code'],
                ],
                [
                    'mlo_details'     => $mlo['mlo_details'],
                    'effective_from'  => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]
            );
        }
    }
}
