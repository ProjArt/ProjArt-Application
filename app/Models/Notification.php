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
        Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . config("services.onesignal.rest_api_key"),
            'Content-Type' => 'application/json',
        ])->post('https://onesignal.com/api/v1/notifications', [
            'app_id' => config("services.onesignal.app_id"),
            "included_segments" => ["Subscribed Users"],
            "name" => "INTERNAL_CAMPAIGN_NAME",
            "contents" => [
                "en" => "COUCOU",
            ],
        ]);
    }
}
