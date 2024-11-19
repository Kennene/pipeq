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
            ['id' => STATUS::WAITING, 'name' => 'statuses.1.name', 'description' => 'statuses.1.description', 'created_at' => now(), 'updated_at' => now()],
            ['id' => STATUS::IN,      'name' => 'statuses.2.name', 'description' => 'statuses.2.description', 'created_at' => now(), 'updated_at' => now()],
            ['id' => STATUS::SERVING, 'name' => 'statuses.3.name', 'description' => 'statuses.3.description', 'created_at' => now(), 'updated_at' => now()],
            ['id' => STATUS::END,     'name' => 'statuses.4.name', 'description' => 'statuses.4.description', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
