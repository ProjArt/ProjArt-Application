<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Notification extends Model
{
    use HasFactory;

    public function notificationType()
    {
        return $this->belongsTo(NotificationType::class);
    }

    public function send()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic YOUR_REST_API_KEY',
            'Content-Type' => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', [
            'body' => '{"included_segments":["Subscribed Users"],"name":"INTERNAL_CAMPAIGN_NAME"}',
        ]);

        Log::info($response->body());
        return $response->json();
    }
}
