<?php

namespace Database\Factories;

use App\Models\Calendar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $calendar = Calendar::factory()->create();
        return [
            "title" => "Event Title",
            "description" => "Event Description",
            "start" => now(),
            "end" => now()->addHour(),
            "location" => "Event Location",
            "calendar_id" => $calendar->id,
        ];
    }
}
