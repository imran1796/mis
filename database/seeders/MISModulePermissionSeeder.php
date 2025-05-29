<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MISModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'exportData-list']);
        Permission::create(['name' => 'exportData-create']);
        Permission::create(['name' => 'exportData-delete']);
        
        Permission::create(['name' => 'mlo-list']);

        Permission::create(['name' => 'mloData-list']);
        Permission::create(['name' => 'mloData-create']);
        Permission::create(['name' => 'mloData-delete']);

        Permission::create(['name' => 'vessel-list']);
        Permission::create(['name' => 'vessel-create']);
        Permission::create(['name' => 'vessel-delete']);

        Permission::create(['name' => 'operatorData-list']);
        Permission::create(['name' => 'operatorData-create']);
        Permission::create(['name' => 'operatorData-delete']);

        Permission::create(['name' => 'reports']);

        Permission::create(['name' => 'turnAround-list']);
        Permission::create(['name' => 'turnAround-create']);
        Permission::create(['name' => 'turnAround-delete']);
    }
}
