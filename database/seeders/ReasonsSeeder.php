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

        for( $i=0; $i<6; $i++) {
            DB::table('reasons')->insert([
                'destination_id' => 1,
                'description' => 'reason.' . $i,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        for( $limit=$i+3 ; $i<$limit; $i++) {
            DB::table('reasons')->insert([
                'destination_id' => 2,
                'description' => 'reason.' . $i,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
