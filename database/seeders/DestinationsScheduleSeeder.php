<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\DestinationsSchedule;

class DestinationsScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $schedules = [];

        foreach ([1, 2] as $destination_id) {
            foreach (range(0, 6) as $day) {
                $schedules[] = [
                    'destination_id' => $destination_id,
                    'day_of_week'    => $day,
                    'open_time'      => '00:00:00',
                    'close_time'     => '23:59:59',
                    'is_closed'      => 0,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
        }

        DestinationsSchedule::insert($schedules);
    }
}
