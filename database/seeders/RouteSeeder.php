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
        // $data = [
        //     ['name' => 'Singapore', 'short_name' => 'SIN', 'code' => 'SGSIN'],
        //     ['name' => 'Colombo', 'short_name' => 'CBO', 'code' => 'LKCMB'],
        //     ['name' => 'Kolkata', 'short_name' => 'CCU', 'code' => 'INCCU'],
        // ];

        // Route::insert($data);

        $routes = [
            ['id' => 1, 'name' => 'Singapore', 'short_name' => 'SIN', 'code' => 'SGSIN'],
            ['id' => 2, 'name' => 'Colombo', 'short_name' => 'CBO', 'code' => 'LKCMB'],
            ['id' => 3, 'name' => 'Kolkata', 'short_name' => 'CCU', 'code' => 'INCCU'],
        ];

        foreach ($routes as $route) {
            Route::updateOrInsert(
                ['id' => $route['id']],
                $route
            );
        }
    }
}
