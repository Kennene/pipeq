<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Krzysztof',
                'email' => 'krzysztof@107.pl',
                'password' => '$2y$12$l1nDLSFrldScVp192xZkZ.5c/h9f9JXPxzKWGTc6gJfOyLpZAHM3K',
                'acl_id' => 40
            ],
            [
                'name' => 'Mateusz',
                'email' => 'mateusz@107.pl',
                'password' => '$2y$12$l1nDLSFrldScVp192xZkZ.5c/h9f9JXPxzKWGTc6gJfOyLpZAHM3K',
                'acl_id' => 40
            ],
        ]);
    }
}
