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
        for( $i=0; $i<3; $i++) {
            DB::table('reasons')->insert([
                'destination_id' => 1,
                'description' => 'reason.' . $i+1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        for( $limit=$i+2 ; $i<$limit; $i++) {
            DB::table('reasons')->insert([
                'destination_id' => 2,
                'description' => 'reason.' . $i+1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $i = $i + 1;
        DB::table('reasons')->insert([
            'destination_id' => 1,
            'description' => 'Not active reason 1. If you see this, you messed up.',
            'is_active' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $i = $i + 1;
        DB::table('reasons')->insert([
            'destination_id' => 2,
            'description' => 'Not active reason 2. If you see this, you messed up.',
            'is_active' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
