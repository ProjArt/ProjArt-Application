<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\GapsUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FakeProfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'username' => 'prof.prof',
            'password' => 'password',
            'role' => User::ROLE_TEACHER,
        ]);

        $gaps_user = GapsUser::create([
            'username' => 'prof.prof',
            'firstname' => 'Prof',
            'name' => 'Prof',
            'is_teacher' => true,
        ]);

        $courses = Course::where('code', 'LIKE', '%dev%')->get();

        $gaps_user->courses()->attach($courses);
    }
}
