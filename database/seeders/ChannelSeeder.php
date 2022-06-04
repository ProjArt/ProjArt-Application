<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (config('database.default') == "mysql") {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        DB::table('channels')->truncate();

        if (config('database.default') == "mysql") {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        Channel::create([
            'name' => config('gaps.username'),
        ]);
    }
}
