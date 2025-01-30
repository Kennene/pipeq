<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Color::insert([
            ['name' => 'primary',   'hex_code' => '#006efa'],
            ['name' => 'secondary', 'hex_code' => '#fa8c00'],
            ['name' => 'green',     'hex_code' => '#77c61e'],
            ['name' => 'red',       'hex_code' => '#eb2959'],
            ['name' => 'yellow',     'hex_code' => '#fea500'],
            ['name' => 'white',     'hex_code' => '#F6F4F7'],
            ['name' => 'black',     'hex_code' => '#18211F'],
            ['name' => 'darkblue',  'hex_code' => '#141f4f'],
        ]);
    }
}
