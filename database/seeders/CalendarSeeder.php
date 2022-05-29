<?php

namespace Database\Seeders;

use App\Models\Calendar;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* $calendar = Calendar::create([
            'name' => 'Calendar Owner'
        ]);

        $user = User::find(1);

        $user->calendars()->attach($calendar->id, ['rights' => Calendar::EDIT_RIGHT]); */
    }
}
