<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContainerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['ctn_size' => 'DC20',   'status' => 'laden', 'teus' => 1],
            ['ctn_size' => 'DC40',   'status' => 'laden', 'teus' => 2],
            ['ctn_size' => 'DC45',   'status' => 'laden', 'teus' => 2],
            ['ctn_size' => 'R20',    'status' => 'laden', 'teus' => 1],
            ['ctn_size' => 'R40',    'status' => 'laden', 'teus' => 2],
            ['ctn_size' => 'MTY20',  'status' => 'empty', 'teus' => 1],
            ['ctn_size' => 'MTY40',  'status' => 'empty', 'teus' => 2],
        ];

        \DB::table('container_types')->insert($data);
    }
}
