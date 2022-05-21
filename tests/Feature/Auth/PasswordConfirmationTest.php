<?php

namespace Tests\Feature\Auth;

use App\Models\Language;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered()
    {
        /* $person = Person::factory()->create();
        $language = Language::factory()->create();

        $user = User::factory()->create(
            [
                'person_id' => $person->id,
                'language_id' => $language->id,
            ]
        );

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200); */
    }

    public function test_password_can_be_confirmed()
    {
       /*  $person = Person::factory()->create();
        $language = Language::factory()->create();

        $user = User::factory()->create(
            [
                'person_id' => $person->id,
                'language_id' => $language->id,
            ]
        );

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors(); */
    }

    public function test_password_is_not_confirmed_with_invalid_password()
    {
        /* $person = Person::factory()->create();
        $language = Language::factory()->create();

        $user = User::factory()->create(
            [
                'person_id' => $person->id,
                'language_id' => $language->id,
            ]
        );
        
        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(); */
    }
}
