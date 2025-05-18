<?php

namespace Database\Seeders;

use App\Models\ContainerType;
use App\Models\Route;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Singapore', 'short_name' => 'SIN', 'code' => 'SGSIN'],
            ['name' => 'Colombo', 'short_name' => 'CBO', 'code' => 'LKCMB'],
            ['name' => 'Kolkata', 'short_name' => 'CCU', 'code' => 'INCCU'],
        ];

        Route::insert($data);
    }
}
