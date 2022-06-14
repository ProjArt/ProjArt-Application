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
        if (config('telegram.bot_token')) {

            TelegraphBot::create([
                'name' => 'TelegramBot',
                'token' => config('telegram.bot_token'),
            ]);

            $url = 'https://api.telegram.org/bot' . config('telegram.bot_token') . '/setWebhook?url=' . env('APP_URL') . '/api/telegram/' . config('telegram.bot_token');
            Http::get($url);
        } else {
            $this->command->error('Telegram bot token is not defined in config/telegram.php');
        }
    }
}
