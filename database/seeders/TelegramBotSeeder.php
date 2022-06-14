<?php

namespace Database\Seeders;

use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class TelegramBotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TelegraphBot::create([
            'name' => 'TelegramBot',
            'token' => config('telegram.bot_token'),
        ]);

        $url = 'https://api.telegram.org/bot' . config('telegram.bot_token') . '/setWebhook?url=' . env('APP_URL') . '/api/telegram/'. config('telegram.bot_token');

        echo $url;
        Http::get($url);
    }
}
