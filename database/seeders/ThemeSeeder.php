<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Theme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('themes')->truncate();

        //thème clair
        Theme::factory()
            ->create([
                'text_color_id' => 1,
                'inactive_color_id' => 2,
                'accent_color_id' => 3,
                'secondary_color_id' => 4,
                'background_color_id' => 5,
                'primary_color_id' => 6,
                'information_color_id' => 7,
            ]);

        //thème foncé TODO : ajouter les couleurs
        Theme::factory()
            ->create([
                'text_color_id' => 1,
                'inactive_color_id' => 2,
                'accent_color_id' => 3,
                'secondary_color_id' => 4,
                'background_color_id' => 5,
                'primary_color_id' => 6,
                'information_color_id' => 7,
            ]);
    }
}
