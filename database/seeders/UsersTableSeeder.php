<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            // [
            //     'name' => 'User',
            //     'email' => 'user@107.pl',
            //     'password' => bcrypt('12345678')
            // ],
            [
                'name' => 'Display',
                'email' => 'display@107.pl',
                'password' => bcrypt('12345678')
            ],
            [
                'name' => 'Coordinator',
                'email' => 'coordinator@107.pl',
                'password' => bcrypt('12345678')
            ],
            [
                'name' => 'Administrator',
                'email' => 'administrator@107.pl',
                'password' => bcrypt('12345678')
            ],
        ]);

        // User::where('email', 'user@107.pl')->first()->roles()->attach(ROLE::USER);
        User::where('email', 'display@107.pl')      ->first()->roles()->attach(ROLE::DISPLAY);
        User::where('email', 'coordinator@107.pl')  ->first()->roles()->attach(ROLE::COORDINATOR);
        User::where('email', 'administrator@107.pl')->first()->roles()->attach(ROLE::ADMINISTRATOR);

        //todo: generate random passwords for default users
    }
}
