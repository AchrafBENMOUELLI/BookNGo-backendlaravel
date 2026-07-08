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
    Hotel::query()->delete();

    Hotel::factory()->create(['categorie' => '5', 'nom' => 'Palace Five']);
    Hotel::factory()->create(['categorie' => '3', 'nom' => 'Budget Three']);

    $response = $this->withHeaders($this->authHeader())
                     ->getJson('/api/hotels?categorie=5&per_page=100');

    $response->assertStatus(200);
    $data = $response->json('data');

    $filtered = array_filter($data, fn($h) => $h['categorie'] === '5');
    $this->assertCount(1, array_values($filtered));
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
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                        ->postJson('/api/admin/hotels', [
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
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test')->plainTextToken;
        $hotel = Hotel::factory()->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                        ->deleteJson("/api/admin/hotels/{$hotel->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }

    public function test_unauthenticated_user_cannot_create_hotel(): void
    {
        $response = $this->postJson('/api/admin/hotels', ['nom' => 'Test']);
        $response->assertStatus(401);
    }

    public function test_cannot_create_hotel_without_name(): void
    {
        $user  = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->postJson('/api/admin/hotels', []);
        $response->assertStatus(422);
    }

    public function test_cannot_get_nonexistent_hotel(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->getJson('/api/hotels/99999');
        $response->assertStatus(404);
    }

    public function test_cannot_update_nonexistent_hotel(): void
    {
        $user  = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->putJson('/api/admin/hotels/99999', ['nom' => 'Test']);
        $response->assertStatus(404);
    }

    public function test_cannot_delete_nonexistent_hotel(): void
    {
        $user  = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->deleteJson('/api/admin/hotels/99999');
        $response->assertStatus(404);
    }
}
