<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelTest extends TestCase
{
    use RefreshDatabase;

    private function authHeader(): array
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        return ['Authorization' => "Bearer $token"];
    }

    public function test_can_get_list_of_hotels(): void
    {
        Hotel::factory()->count(5)->create();

        $response = $this->withHeaders($this->authHeader())
                         ->getJson('/api/hotels');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'meta']);
    }

    public function test_can_filter_hotels_by_categorie(): void
    {
        Hotel::factory()->create(['categorie' => '5']);
        Hotel::factory()->create(['categorie' => '3']);

        $response = $this->withHeaders($this->authHeader())
                         ->getJson('/api/hotels?categorie=5');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('5', $data[0]['categorie']);
    }

    public function test_can_search_hotels_by_nom(): void
    {
        Hotel::factory()->create(['nom' => 'Palace Tunis']);
        Hotel::factory()->create(['nom' => 'Beach Resort']);

        $response = $this->withHeaders($this->authHeader())
                         ->getJson('/api/hotels?search=Palace');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Palace Tunis', $data[0]['nom']);
    }

    public function test_can_get_single_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->withHeaders($this->authHeader())
                         ->getJson("/api/hotels/{$hotel->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['nom' => $hotel->nom]);
    }

    public function test_authenticated_user_can_create_hotel(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/hotels', [
                             'nom'           => 'New Hotel',
                             'categorie'     => '4',
                             'adresse'       => 'Tunis',
                             'prix_unitaire' => 200,
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('hotels', ['nom' => 'New Hotel']);
    }

    public function test_authenticated_user_can_delete_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->withHeaders($this->authHeader())
                         ->deleteJson("/api/hotels/{$hotel->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }
}
