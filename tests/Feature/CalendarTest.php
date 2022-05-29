<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $user =  User::findOr(1, function () {
            return User::factory()->create();
        });
        Sanctum::actingAs(
            $user
        );

        $calendar = Calendar::factory()->create();

        $user->calendars()->attach($calendar->id, ["rights" => Calendar::EDIT_RIGHT]);

        $response = $this->getJson('/api/calendars/' . $calendar->id);

        $response->assertSuccessful();
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

        $calendar->forceDelete();
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

        Calendar::whereName('test')->forceDelete();
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

        $user->calendars()->attach($calendar->id, ["rights" => Calendar::EDIT_RIGHT]);

        $response = $this->putJson('/api/calendars/' . $calendar->id, [
            'name' => 'test',
        ]);

        $response->assertStatus(200);

        $calendar->forceDelete();
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

        $calendar->refresh()->forceDelete();
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

        $user->calendars()->attach($calendar->id, ["rights" => Calendar::EDIT_RIGHT]);

        $response = $this->deleteJson('/api/calendars/' . $calendar->id);



        $response->assertStatus(200);

        $this->assertSoftDeleted('calendars', ["id" => $calendar->id]);

        //$calendar->forceDelete();
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
        $calendar->forceDelete();
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
        $user->calendars()->attach($calendar->id, ["rights" => Calendar::EDIT_RIGHT]);

        $userToShare = User::firstOrCreate(
            [
                'username' => 'test',
            ],
            [
                "password" => "password",
                "theme_id" => 1
            ]
        );

        $response = $this->postJson('/api/calendars/share', [
            'calendar_id' => $calendar->id,
            'user_id' => $userToShare->id,
            'can_view' => 1,
            'can_own' => 1,
        ]);

        $response->assertStatus(200);


        $userToShare->delete();
        $calendar->forceDelete();
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

        $userToShare = User::firstOrCreate(
            [
                'username' => 'test',
            ],
            [
                "password" => "password",
                "theme_id" => 1
            ]
        );

        $response = $this->postJson('/api/calendars/share', [
            'calendar_id' => $calendar->id,
            'user_id' => $userToShare->id,
            'can_view' => 1,
            'can_own' => 1,
        ]);


        $response->assertStatus(403);

        $userToShare->delete();
        Calendar::whereName("Calendar name")->forceDelete();
    }

    /*  public function test_can_import_ics()
    {
        /$user = User::findOr(1, function () {
            return User::factory()->create();
        });

        Sanctum::actingAs(
            $user
        );

        $eventCount = Event::count();

        $filename = Storage::path('tests/test.ics');
        $file = new UploadedFile($filename, 'test.ics', 'text/calendar', filesize($filename), 0, true);

 

          $response = $this->postJson('/api/calendars/import', [
            'name' => "testImport",
            'ics' => $file,
        ]);

        dd($response->json()); 
    } */
}
