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

        // Schedule for destination_id = 1
        DestinationsSchedule::insert([
            [
                'destination_id' => 1,
                'day_of_week'   => 1, // Monday (PON)
                'open_time'     => '12:00',
                'close_time'    => '17:00',
                'is_closed'     => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'destination_id' => 1,
                'day_of_week'   => 3, // Wednesday (ŚRO)
                'open_time'     => '08:00',
                'close_time'    => '14:00',
                'is_closed'     => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'destination_id' => 1,
                'day_of_week'   => 5, // Friday (PIĄ)
                'open_time'     => '12:00',
                'close_time'    => '17:00',
                'is_closed'     => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'destination_id' => 1,
                'day_of_week'   => 6, // Saturday (SOB)
                'open_time'     => '07:30',
                'close_time'    => '14:00',
                'is_closed'     => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ]);

        // Schedule for destination_id = 2
        DestinationsSchedule::insert([
            [
                'destination_id' => 2,
                'day_of_week'   => 1, // Monday (PON)
                'open_time'     => '14:00',
                'close_time'    => '17:00',
                'is_closed'     => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'destination_id' => 2,
                'day_of_week'   => 3, // Wednesday (ŚRO)
                'open_time'     => '12:00',
                'close_time'    => '14:00',
                'is_closed'     => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'destination_id' => 2,
                'day_of_week'   => 5, // Friday (PIĄ)
                'open_time'     => '14:00',
                'close_time'    => '17:00',
                'is_closed'     => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'destination_id' => 2,
                'day_of_week'   => 6, // Saturday (SOB)
                'open_time'     => '07:30',
                'close_time'    => '14:00',
                'is_closed'     => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ]);

        // closed days
        DestinationsSchedule::insert([
            // Destination 1:
            ['destination_id'=>1,'day_of_week'=>0,'is_closed'=>1],
            ['destination_id'=>1,'day_of_week'=>2,'is_closed'=>1],
            ['destination_id'=>1,'day_of_week'=>4,'is_closed'=>1],

            // Destination 2:
            ['destination_id'=>2,'day_of_week'=>0,'is_closed'=>1],
            ['destination_id'=>2,'day_of_week'=>2,'is_closed'=>1],
            ['destination_id'=>2,'day_of_week'=>4,'is_closed'=>1],
        ]);
    }
}
