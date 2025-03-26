<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Str::random(12);

        if(config('app.env') === 'local') {
            $password = '12345678';
        }
        
        User::insert([
            [
                'name' => 'Display',
                'email' => 'display@107.pl',
                'password' => bcrypt($password)
            ],
            [
                'name' => 'Coordinator',
                'email' => 'coordinator@107.pl',
                'password' => bcrypt($password)
            ],
            [
                'name' => 'Administrator',
                'email' => 'administrator@107.pl',
                'password' => bcrypt($password)
            ],
        ]);

        User::where('email', 'display@107.pl')      ->first()->roles()->attach(ROLE::DISPLAY);
        User::where('email', 'coordinator@107.pl')  ->first()->roles()->attach(ROLE::COORDINATOR);
        User::where('email', 'administrator@107.pl')->first()->roles()->attach(ROLE::ADMINISTRATOR);

        echo "\033[1;33m------------------------------------------------------------------------------\n";
        echo "Default password for Display, Coordinator, and Administrator is: $password\n";
        echo "------------------------------------------------------------------------------\033[0m\n";
    }
}
