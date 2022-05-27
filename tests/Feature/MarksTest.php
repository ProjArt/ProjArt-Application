<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MarksTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_access_marks()
    {
        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );
        $response = $this->getJson('/api/marks');

        $response->assertStatus(200);
    }

    public function test_cannot_access_marks_if_not_logged_in()
    {
        $response = $this->getJson('/api/marks');

        $response->assertUnauthorized();
    }

    public function test_marks_have_good_format()
    {
        Sanctum::actingAs(
            User::findOr(1, function () {
                return User::factory()->create();
            }),
        );
        $response = $this->getJson('/api/marks');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [ //any year
                    '*' => [ //any marks
                        'id',
                        'module_code',
                        'module_name',
                        'value',
                        'year_start',
                        'year_end',
                    ],
                ],
            ],
        ]);
    }
}
