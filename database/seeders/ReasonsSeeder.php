<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Registratr's Office
        DB::table('reasons')->insert([
            'destination_id' => 1,
            'description' => 'reason.registrar.submission',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('reasons')->insert([
            'destination_id' => 1,
            'description' => 'reason.registrar.idcard',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('reasons')->insert([
            'destination_id' => 1,
            'description' => 'reason.registrar.diploma',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('reasons')->insert([
            'destination_id' => 1,
            'description' => 'reason.registrar.certificate',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Payments office
        DB::table('reasons')->insert([
            'destination_id' => 2,
            'description' => 'reason.payment.scholarship',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('reasons')->insert([
            'destination_id' => 2,
            'description' => 'reason.payment.request',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('reasons')->insert([
            'destination_id' => 2,
            'description' => 'reason.payment.issue',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
