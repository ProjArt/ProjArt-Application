<?php

namespace Tests\Feature\Auth;

use App\Models\Language;
use App\Models\Person;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $person = Person::factory()->create();
        $language = Language::factory()->create();

        $user = User::factory()->create(
            [
                'person_id' => $person->id,
                'language_id' => $language->id
            ]
        );


        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => "password",
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {

        $person = Person::factory()->create();
        $language = Language::factory()->create();

        $user = User::factory()->create(
            [
                'person_id' => $person->id,
                'language_id' => $language->id
            ]
        );
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}
