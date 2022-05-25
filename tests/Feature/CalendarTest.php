<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_access_own_calendar()
    {
        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );
        $response = $this->getJson('/api/calendars');

        $response->assertStatus(200);
    }

    public function test_can_access_specific_calendar()
    {
        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );
        $response = $this->getJson('/api/calendars/1');

        $response->assertStatus(200);
    }

    public function test_cannot_access_calendar_if_not_own()
    {
        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );

        $calendar = Calendar::factory()->create();

        $response = $this->getJson('/api/calendars/' . $calendar->id);

        $response->assertStatus(403);

        $calendar->delete();
    }

    public function test_can_create_calendar()
    {
        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );
        $response = $this->postJson('/api/calendars', [
            'name' => 'test',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('calendars', [
            'name' => 'test',
        ]);

        Calendar::whereName('test')->first()->delete();
    }

    public function test_update_calendar_name()
    {

        $user = User::findOr(1, function () {
            return User::factory()->create();
        });
        Sanctum::actingAs(
            $user
        );

        $calendar = Calendar::factory()->create();

        $user->calendarsOwn()->attach($calendar);

        $response = $this->putJson('/api/calendars/' . $calendar->id, [
            'name' => 'test',
        ]);

        $response->assertStatus(200);
    }

    public function test_cannot_update_calendar_name_if_not_own()
    {
        $user = User::findOr(1, function () {
            return User::factory()->create();
        });

        Sanctum::actingAs(
            $user
        );
        $calendar = Calendar::factory()->create();
        $response = $this->putJson('/api/calendars/' . $calendar->id, [
            'name' => 'test',
        ]);
        $response->assertStatus(403);

        $calendar->delete();
    }

    public function test_can_delete_calendar()
    {
        $user = User::findOr(1, function () {
            return User::factory()->create();
        });
        Sanctum::actingAs(
            $user
        );

        $calendar = Calendar::factory()->create();

        $user->calendarsOwn()->attach($calendar);

        $response = $this->deleteJson('/api/calendars/' . $calendar->id);

        $response->assertStatus(200);

        $calendar->delete();
    }

    public function test_cannot_delete_calendar_name_if_not_own()
    {
        $user = User::findOr(1, function () {
            return User::factory()->create();
        });

        Sanctum::actingAs(
            $user
        );
        $calendar = Calendar::factory()->create();
        $response = $this->deleteJson('/api/calendars/' . $calendar->id);
        $response->assertStatus(403);
        $calendar->delete();
    }

    public function test_can_share_calendar()
    {
        $user = User::findOr(1, function () {
            return User::factory()->create();
        });

        Sanctum::actingAs(
            $user
        );
        $calendar = Calendar::factory()->create();
        $user->calendarsOwn()->attach($calendar);

        $userToShare = User::factory()->create([
            'username' => "test"
        ]);

        $response = $this->postJson('/api/calendars/share', [
            'calendar_id' => $calendar->id,
            'user_id' => $userToShare->id,
            'can_view' => 1,
            'can_own' => 1,
        ]);

        $userToShare->delete();
        $calendar->delete();
        $response->assertStatus(200);
    }

    public function test_cannot_share_if_not_own()
    {
        $user = User::findOr(1, function () {
            return User::factory()->create();
        });

        Sanctum::actingAs(
            $user
        );
        $calendar = Calendar::factory()->create();

        $userToShare = User::factory()->create([
            'username' => "test"
        ]);

        $response = $this->postJson('/api/calendars/share', [
            'calendar_id' => $calendar->id,
            'user_id' => $userToShare->id,
            'can_view' => 1,
            'can_own' => 1,
        ]);

        $userToShare->delete();
        $calendar->delete();
        $response->assertStatus(403);
    }
}
