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
            [
                'name' => 'Krzysztof',
                'email' => 'krzysztof@107.pl',
                'password' => bcrypt('12345678')
            ],
            [
                'name' => 'Mateusz',
                'email' => 'mateusz@107.pl',
                'password' => bcrypt('12345678')
            ],
            [
                'name' => 'User',
                'email' => 'normal@107.pl',
                'password' => bcrypt('12345678')
            ],
            [
                'name' => 'Displayer',
                'email' => 'display@107.pl',
                'password' => bcrypt('12345678')
            ],
            [
                'name' => 'Fixer',
                'email' => 'coordinator@107.pl',
                'password' => bcrypt('12345678')
            ],
        ]);

        User::where('email', 'normal@107.pl')->first()->roles()->attach(ROLE::USER);
        User::where('email', 'display@107.pl')->first()->roles()->attach(ROLE::DISPLAY);
        User::where('email', 'coordinator@107.pl')->first()->roles()->attach(ROLE::COORDINATOR);

        User::where('email', 'krzysztof@107.pl')->first()->roles()->attach(ROLE::ADMINISTRATOR);
        User::where('email', 'mateusz@107.pl')->first()->roles()->attach(ROLE::ADMINISTRATOR);
    }
}
