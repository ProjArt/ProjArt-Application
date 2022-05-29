<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\Event;
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
                    'id',
                    'name',
                    'can_edit',
                    'events' => [
                        '*' => [
                            'id',
                            'title',
                            'start',
                            'end',
                            'description',
                            'location',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_can_create_event()
    {
        $user  = User::findOr(1, function () {
            return User::factory()->create();
        });
        Sanctum::actingAs(
            $user
        );

        $calendar = Calendar::factory()->create();

        $user->calendars()->attach($calendar->id, ['rights' => Calendar::EDIT_RIGHT]);

        $response = $this->json(
            "POST",
            route('api.events.store'),
            [
                'title' => 'Test Event',
                'start' => '2020-01-01',
                'end' => '2020-01-01',
                'description' => 'Test Event',
                'location' => 'Test Event',
                'calendar_id' => $calendar->id,
            ],
        );

        $response->assertSuccessful();

        $this->assertDatabaseHas('events', [
            'title' => 'Test Event',
            'start' => '2020-01-01',
            'end' => '2020-01-01',
            'description' => 'Test Event',
            'location' => 'Test Event',
            'calendar_id' => $calendar->id,
        ]);

        Event::where('title', 'Test Event')->delete();
        $calendar->forceDelete();
    }

    public function test_can_update_event()
    {
        // return;
        $user  = User::findOr(1, function () {
            return User::factory()->create();
        });

        Sanctum::actingAs(
            $user
        );

        $calendar = Calendar::factory()->create();

        $user->calendars()->attach($calendar->id, ['rights' => Calendar::EDIT_RIGHT]);

        $event = Event::factory()->create([
            'calendar_id' => $calendar->id,
        ]);

        $response = $this->json(
            "PUT",
            route('api.events.update', $event->id),
            [
                'title' => 'Test Event2',
            ],
        );

        $response->assertSuccessful();

        $this->assertDatabaseHas('events', [
            'title' => 'Test Event2',
        ]);

        $event->delete();
        Calendar::whereName("Calendar name")->forceDelete();
    }
}
