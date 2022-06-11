<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $all = 1;
        if (config('database.default') == "mysql") {
            $all = $this->command->choice('Voulez-vous seeder toutes les tables pour tester (1) ou que le nécessaire au fonctionnement ? (0) ', ['1', '0'], 1);
        }

        if (config('database.default') == "mysql") {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        //DB::table('horaires')->truncate();
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(ColorSeeder::class);
        $this->call(ThemeSeeder::class);
        $this->call(ClassroomSeeder::class);


        if ($all == 1) {
            $this->call(UsersSeeder::class);
            DB::table('classroom_user')->truncate();
            User::find(1)->classrooms()->attach('M49-1');
            DB::table('calendars')->truncate();
            DB::table('calendar_user')->truncate();
            $this->call(EventSeeder::class);
            $this->call(MarkSeeder::class);
            $this->call(CalendarSeeder::class);
            $this->call(AbsencesSeeder::class);
            $this->call(GapsUsersSeeder::class);
            $this->call(MenuSeeder::class);
            $this->call(ChannelSeeder::class);
            //$this->call(NotificationSeeder::class);
            User::find(1)->channels()->attach(config('gaps.username'));
        }


        if (config('database.default') == "mysql") {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
