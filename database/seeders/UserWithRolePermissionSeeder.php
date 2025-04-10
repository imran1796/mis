<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserWithRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'user-list']);
        Permission::create(['name' => 'user-create']);
        Permission::create(['name' => 'user-edit']);
        Permission::create(['name' => 'user-delete']);
        Permission::create(['name' => 'role-list']);
        Permission::create(['name' => 'role-create']);
        Permission::create(['name' => 'role-edit']);
        Permission::create(['name' => 'role-delete']);
        Permission::create(['name' => 'permission-list']);
        Permission::create(['name' => 'permission-create']);
        Permission::create(['name' => 'permission-edit']);
        Permission::create(['name' => 'permission-delete']);

        $adminRole = Role::create(['name' => 'system-admin']);
        $adminRole->givePermissionTo(['user-list','user-create','user-edit','user-delete','role-list','role-create','role-edit','role-delete','permission-list','permission-create','permission-edit','permission-delete']);

        $systemAdmin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@globelink.com',
            'password' => Hash::make('123456')
        ]);

        $systemAdmin->assignRole('system-admin');
    }
}
