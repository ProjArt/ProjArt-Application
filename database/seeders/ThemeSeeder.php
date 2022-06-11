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
                'name' => 'light',
                'text_color_primary_id' => 1,
                'text_color_secondary_id' => 2,
                'inactive_color_id' => 3,
                'accent_color_id' => 4,
                'information_color_id' => 5,
                'primary_color_id' => 5,
                'secondary_color_id' => 6,
                'background_color_id' => 2,
                'information_color_id' => 7,
                'border_color_id' => 7,
            ]);



        Theme::factory()
            ->create([
                'name' => 'blue',
                'text_color_primary_id' => 2,
                'text_color_secondary_id' => 2,
                'inactive_color_id' => 8,
                'accent_color_id' => 4,
                'primary_color_id' => 5,
                'secondary_color_id' => 6,
                'background_color_id' => 5,
                'information_color_id' => 9,
                'border_color_id' => 9,
            ]);
        Theme::factory()
            ->create([
                'name' => 'dark',
                'text_color_primary_id' => 2,
                'text_color_secondary_id' => 10,
                'inactive_color_id' => 3,
                'accent_color_id' => 2,
                'primary_color_id' => 2,
                'secondary_color_id' => 2,
                'background_color_id' => 10,
                'information_color_id' => 10,
                'border_color_id' => 2,
            ]);
    }
}
