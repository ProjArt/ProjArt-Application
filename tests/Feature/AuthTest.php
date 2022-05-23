<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase;

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
            User::factory()->create(),
        );

        $response = $this->get('/api/me');

        $response->assertOk();
    }

    public function test_login()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', '/api/login', [
            'username' => $user->username,
            'password' => $user->password,
        ]);

        $response->assertOk();
    }

    public function test_login_goes_wrong()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', '/api/login', [
            'username' => $user->username,
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized();
    }

    public function test_register()
    {
        $response = $this->json('POST', '/api/register', [
            'username' => 'test',
            'password' => 'password',
        ]);

        $response->assertOk();
    }

    public function test_register_goes_wrong_because_credentials()
    {
        $response = $this->json('POST', '/api/register', [
            'name' => 'test',
            'password' => 'password',
        ]);
        $response->assertStatus(422);
    }

    public function test_register_fail_username_already_taken() {
        $response = $this->json('POST', '/api/register', [
            'username' => 'test',
            'password' => 'password',
        ]);

        $response->assertOk();

        $response = $this->json('POST', '/api/register', [
            'username' => 'test',
            'password' => 'password',
        ]);

        $response->assertUnauthorized();
    }

    public function test_can_logout_if_logged() {

        Sanctum::actingAs(
            User::factory()->create(),
        );

        $response = $this->get('/api/logout');

        $response->assertOk();
    }

    public function test_cannot_logout_if_not_logged() {

        $response = $this->json('GET', '/api/logout', []);

        $response->assertUnauthorized();
    }
}
