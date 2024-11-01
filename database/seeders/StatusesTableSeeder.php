<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Status;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::insert([
            ['name' => 'statuses.1.name', 'description' => 'statuses.1.description'],
            ['name' => 'statuses.2.name', 'description' => 'statuses.2.description'],
            ['name' => 'statuses.3.name', 'description' => 'statuses.3.description'],
            ['name' => 'statuses.4.name', 'description' => 'statuses.4.description'],
        ]);
    }
}
