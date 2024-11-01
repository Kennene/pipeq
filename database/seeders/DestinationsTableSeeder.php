<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Destination;

class DestinationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Destination::insert([
            ['name' => 'destinations.1.name', 'description' => 'destinations.1.description'],
            ['name' => 'destinations.2.name', 'description' => 'destinations.2.description'],
        ]);
    }
}
