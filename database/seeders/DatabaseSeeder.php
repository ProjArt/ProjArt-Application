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
        if(config('database.default') == "mysql") {
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
        $this->call(UsersSeeder::class);
        DB::table('classroom_user')->truncate();
        User::find(1)->classrooms()->attach('M49-1');
        $this->call(EventSeeder::class);
        $this->call(CalendarSeeder::class);
        $this->call(CalendarUserFollowSeeder::class);
        if(config('database.default') == "mysql") {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
