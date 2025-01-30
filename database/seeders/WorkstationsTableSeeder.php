<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Workstation;

class WorkstationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Workstation::insert([
            ['name' => 'workstations.1.name', 'destination_id' => 1],
            ['name' => 'workstations.2.name', 'destination_id' => 1],
            ['name' => 'workstations.3.name', 'destination_id' => 1],
            ['name' => 'workstations.4.name', 'destination_id' => 1],
            ['name' => 'workstations.5.name', 'destination_id' => 2],
        ]);
    }
}
