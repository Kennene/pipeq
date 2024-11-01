<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AclSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('acl')->insert([
            [
                'id' => 1,
                'name' => 'User',
                'description' => 'Standard user with access to create and see their own tickets.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 20,
                'name' => 'Coordinator',
                'description' => 'Coordinator with access to assign and manage ticket statuses.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 30,
                'name' => 'API',
                'description' => 'API role for automated tasks and system integrations.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 40,
                'name' => 'Administrator',
                'description' => 'Administrator with full access to manage tickets, users, and system configurations.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
