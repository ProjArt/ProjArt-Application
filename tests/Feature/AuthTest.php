<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{

    public function test_non_authenticated_user_cannot_access_protected_routes()
    {
        $response = $this->get('/api/me', [
            "Accept" => "application/json", 
        ]);

        $response->assertStatus(401);
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
            'password' => 'password',
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

        $response->assertStatus(401);
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
}
