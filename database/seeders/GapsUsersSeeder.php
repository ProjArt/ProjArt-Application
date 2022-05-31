<?php

namespace Database\Seeders;

use App\Http\Services\GapsUsersService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GapsUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('gaps_users')->truncate();
        DB::table('courses')->truncate();

        GapsUsersService::fetchAllUsers();
    }
}
