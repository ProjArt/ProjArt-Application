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

    public function send($title, $message, $to = ["all"])
    {

        $response =  Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => "Bearer " . config('broadcasting.connections.pusher.key'),
            'Content-Type' => 'application/json',
        ])->post(
            'https://' . config('broadcasting.connections.pusher.app_id') . '.pushnotifications.pusher.com/publish_api/v1/instances/' . config('broadcasting.connections.pusher.app_id') . '/publishes',
            [
                "interests" => $to,
                "web" => [
                    "notification" => [
                        "title" => $title,
                        "body" => $message,
                    ],
                ]
            ]
        );

        Log::info($response->json());
    }
}

/*curl  -H "Content-Type: application/json"      
        -H "Authorization: Bearer E41ABF4266A110BFD30410FAA3C24484E81EEE814EC3C41E0E07BFF7B289A3B0"      
        -X POST "https://9daf55df-fcab-4d64-b2d7-5d7a2a56b4ee.pushnotifications.pusher.com/publish_api/v1/instances/9daf55df-fcab-4d64-b2d7-5d7a2a56b4ee/publishes"      
        -d '{"interests":["vincent.tarrit"],"web":{"notification":{"title":"Hello","body":"Hello, wVince!"}}}'*/
