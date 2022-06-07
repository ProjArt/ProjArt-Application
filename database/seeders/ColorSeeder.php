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
            'value' => '#3A3C3D',
        ]);

        Color::factory()->create([
            'value' => '#929194',
        ]);

        Color::factory()->create([
            'value' => '#F93B58',
        ]);

        Color::factory()->create([
            'value' => '#E72128',
        ]);

        Color::factory()->create([
            'value' => '#FFFFFF',
        ]);

        Color::factory()->create([
            'value' => '#314178',
        ]);

        Color::factory()->create([
            'value' => '#F0F0F0',
        ]);
    }
}
