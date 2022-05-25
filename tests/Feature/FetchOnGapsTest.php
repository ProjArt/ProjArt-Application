<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FetchOnGapsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_cannot_fetch_on_gaps_if_not_connected()
    {
        $response = $this->json('GET', route('api.fetch.gaps.events'));

        $response->assertUnauthorized();
    }

    public function test_fetch_on_gaps_failed_if_user_not_on_gaps()
    {
        $user =  User::factory()->create([
            "username" => "test" . date("YmdHis"),
            "password" => "testtest",
        ]);
        Sanctum::actingAs(
            $user
        );

        $response = $this->json('GET', route('api.fetch.gaps.events'));

        $response->assertUnauthorized();


        $user->delete();
    }
}
