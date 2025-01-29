<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DestinationsScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = DB::table('destinations')->get();

        foreach ($destinations as $destination) {
            $schedules = [];

            for ($day = 0; $day <= 6; $day++) {
                // mon - fri
                if ($day >= 1 && $day <= 5) {
                    $schedules[] = [
                        'destination_id' => $destination->id,
                        'day_of_week' => $day,
                        'open_time' => '01:00:00',
                        'close_time' => '23:00:00',
                        'is_closed' => false
                    ];
                } else {
                // sat - sun
                    $schedules[] = [
                        'destination_id' => $destination->id,
                        'day_of_week' => $day,
                        'open_time' => '01:00:00',
                        'close_time' => '23:00:00',
                        'is_closed' => false
                    ];
                }
            }

            DB::table('destinations_schedules')->insert($schedules);
        }
    }
}
