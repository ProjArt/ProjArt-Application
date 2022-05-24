<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EventTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_access_events_route()
    {
        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );

        $response = $this->json("GET", route('api.events.index'));


        $response->assertStatus(200);
    }

    public function test_events_return_array_events()
    {
        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );

        $response = $this->json("GET", route('api.events.index'));

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    '*' => [
                        'id',
                        'calendar_id',
                        'title',
                        'description',
                        'start',
                        'end',
                        'location',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ]);
    }

    public function test_can_access_specific_calendar()
    {
        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );

        $response = $this->json("GET", route('api.getCalendarEvents', 1));

        $response->assertStatus(200);
    }
}
