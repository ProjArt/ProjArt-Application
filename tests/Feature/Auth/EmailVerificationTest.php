<?php

namespace Tests\Feature\Auth;

use App\Models\Language;
use App\Models\Person;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered()
    {
        $person = Person::factory()->create();
        $language = Language::factory()->create();

        $user = User::factory()->create(
            [
                'person_id' => $person->id,
                'language_id' => $language->id
            ]
        );

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified()
    {
        $person = Person::factory()->create();
        $language = Language::factory()->create();

        $user = User::factory()->create(
            [
                'person_id' => $person->id,
                'language_id' => $language->id,
                'email_verified_at' => null,
            ]
        );

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(RouteServiceProvider::HOME.'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash()
    {
        $person = Person::factory()->create();
        $language = Language::factory()->create();

        $user = User::factory()->create(
            [
                'person_id' => $person->id,
                'language_id' => $language->id,
                'email_verified_at' => null,
            ]
        );

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
