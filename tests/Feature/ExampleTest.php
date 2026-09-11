<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_head_of_school_can_access_leader_dashboard(): void
    {
        $user = \App\Models\User::factory()->make([
            'role' => 'Head of School',
            'username' => 'hos_test',
        ]);

        $response = $this->actingAs($user)->get(route('leader.dashboard'));
        $response->assertStatus(200);
    }

    public function test_headmistress_can_access_leader_dashboard(): void
    {
        $user = \App\Models\User::factory()->make([
            'role' => 'Headmistress',
            'username' => 'headmistress_test',
        ]);

        $response = $this->actingAs($user)->get(route('leader.dashboard'));
        $response->assertStatus(200);
    }
}
