<?php

namespace Database\Seeders;

use App\Http\Services\GapsEventsService;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->truncate();
        GapsEventsService::fetchAllHoraires(User::find(1));
    }
}
