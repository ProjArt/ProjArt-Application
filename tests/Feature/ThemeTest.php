<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_all_theme_if_logged()
    {
        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );
        $response = $this->getJson(route('api.themes.index'));

        $response->assertStatus(200);
    }

    public function test_get_all_theme_if_not_logged()
    {
        $response = $this->getJson(route('api.themes.index'));

        $response->assertStatus(401);
    }

    public function test_set_theme_if_logged()
    {
        $user = User::findOr(1, function () {
            return User::factory()->create();
        });
        Sanctum::actingAs(
            $user
        );
        $response = $this->postJson(route('api.user.setTheme'), [
            'theme_id' => 1,
        ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals(1, $user->theme_id);
    }
}
