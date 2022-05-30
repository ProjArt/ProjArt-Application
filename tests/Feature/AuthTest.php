<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\Classroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;

use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    public function test_non_authenticated_user_cannot_access_protected_routes()
    {
        $response = $this->get('/api/me', [
            "Accept" => "application/json",
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_access_protected_routes()
    {

        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );

        $response = $this->get('/api/me');

        $response->assertOk();
    }

    public function test_login()
    {
        $user = User::findOr(1, function () {
            return User::factory()->create();
        });

        $response = $this->json('POST', '/api/login', [
            'username' => $user->username,
            'password' => $user->password,
        ]);

        $response->assertOk();
    }

    public function test_login_goes_wrong()
    {
        $user = User::findOr(1, function () {
            return User::factory()->create();
        });

        $response = $this->json('POST', '/api/login', [
            'username' => $user->username,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_register()
    {
        $response = $this->json('POST', '/api/register', [
            'username' => config('gaps.username'),
            'password' => config('gaps.password'),
        ]);

        $this->assertDatabaseHas('users', [
            'username' => config('gaps.username'),
        ]);
    }

    public function test_register_goes_wrong_because_credentials()
    {
        $response = $this->json('POST', '/api/register', [
            'name' => 'test',
            'password' => 'password',
        ]);
        $response->assertStatus(422);
    }

    public function test_register_fail_username_already_taken()
    {
        $user = User::firstOrCreate([
            'username' => config('gaps.username'),
        ], [
            'password' => config('gaps.password'),
        ]);

        $this->assertDatabaseHas('users', ['username' => $user->username]);

        $response = $this->json('POST', '/api/register', [
            'username' => $user->username,
            'password' => config('gaps.password'),
        ]);

        $response->assertUnauthorized();
    }

    public function test_can_logout_if_logged()
    {

        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );

        $response = $this->get('/api/logout');

        $response->assertOk();
    }

    public function test_cannot_logout_if_not_logged()
    {

        $response = $this->json('GET', '/api/logout', []);

        $response->assertUnauthorized();
    }

    public function test_register_with_classroom()
    {
        $username = Str::random(10);
        $classroom = Classroom::firstOrCreate([
            'name' => 'test',
        ]);

        $calendar = Calendar::firstOrCreate([
            "name" => "test",
        ]);

        $this->assertDatabaseHas('classrooms', [
            'name' => $classroom->name,
        ]);

        $response = $this->json('POST', '/api/register', [
            'username' => $username,
            'password' => "password",
            'classroom_name' => "test",
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'username' => $username,
        ]);


        $this->assertDatabaseHas('classroom_user', [
            'classroom_name' => $classroom->name,
            'user_id' => User::where('username', $username)->first()->id,
        ]);

        Classroom::whereName($classroom->name)->delete();
        User::whereUsername($username)->delete();
    }

    public function test_fail_register_if_calendar_not_known()
    {
        $username = Str::random(10);
        $classroom = Classroom::firstOrCreate([
            'name' => 'test-not-existing',
        ]);

        $response = $this->json('POST', '/api/register', [
            'username' => $username,
            'password' => "password",
            'classroom_name' => $classroom->name,
        ]);

        $response->assertStatus(404);

        $classroom->delete();
    }
}
