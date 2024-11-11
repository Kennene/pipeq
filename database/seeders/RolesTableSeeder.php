<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // id are seperated by 10 to allow for future roles to be added in between
        DB::table('roles')->insert([
            [
                'id' => ROLE::USER,
                'name' => 'User',
                'description' => 'Standard user with access to create and see their own tickets.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => ROLE::DISPLAY,
                'name' => 'Display',
                'description' => 'Display role with access to see all tickets.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => ROLE::COORDINATOR,
                'name' => 'Coordinator',
                'description' => 'Coordinator with access to assign and manage ticket statuses.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => ROLE::API,
                'name' => 'API',
                'description' => 'API role for automated tasks and system integrations.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => ROLE::ADMINISTRATOR,
                'name' => 'Administrator',
                'description' => 'Administrator with full access to manage tickets, users, and system configurations.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
