<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notifications')->truncate();

        Notification::create([
            'title' => 'Welcome to Gaps!',
            'text' => 'This is a notification that will be sent to all users when the application is installed.',
            'channel_name' => config('gaps.username'),
        ]);
    }
}
