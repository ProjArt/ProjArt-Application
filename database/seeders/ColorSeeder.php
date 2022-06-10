<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('colors')->truncate();

        Color::factory()->create([
            'value' => '#3A3C3D', //1
        ]);

        Color::factory()->create([
            'value' => '#FFFFFF', //2
        ]);

        Color::factory()->create([
            'value' => '#929194', //3
        ]);

        Color::factory()->create([
            'value' => '#F93B58', //4
        ]);

        Color::factory()->create([
            'value' => '#314178', //5
        ]);

        Color::factory()->create([
            'value' => '#E72128', //6
        ]);

        Color::factory()->create([
            'value' => '#F0F0F0', //7
        ]);

        Color::factory()->create([
            'value' => '#5E6C9D', // 8
        ]);

        Color::factory()->create([
            'value' => '#182047', // 9
        ]);

        Color::factory()->create([
            'value' => '#000000', // 10
        ]);
    }
}
