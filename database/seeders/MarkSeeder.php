<?php

namespace Database\Seeders;

use App\Http\Services\GapsMarksService;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('marks')->truncate();
        GapsMarksService::fetchAllNotes(User::find(1));
    }
}
