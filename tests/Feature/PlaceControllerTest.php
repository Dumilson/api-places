<?php

namespace Tests\Feature;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_place()
    {
        $response = $this->postJson('/api/v1/places', [
            'name' => 'Central Park',
            'city' => 'New York',
            'state' => 'NY'
        ]);

        $response->assertStatus(201);
    }

    public function test_can_list_places()
    {
        $response = $this->getJson('/api/v1/places');

        $response->assertStatus(200);
    }

    public function test_can_show_place()
    {
        $place = Place::factory()->create();

        $response = $this->getJson('/api/v1/places/' . $place->id);

        $response->assertStatus(200);
    }

    public function test_can_update_place()
    {
        $place = Place::factory()->create();

        $response = $this->putJson('/api/v1/places/' . $place->id, [
            'name' => 'Updated Park',
            'city' => 'Updated City',
            'state' => 'UC'
        ]);

        $response->assertStatus(201);
    }

    public function test_can_delete_place()
    {
        $place = Place::factory()->create();

        $response = $this->deleteJson('/api/v1/places/' . $place->id);

        $response->assertStatus(200);
    }
}
