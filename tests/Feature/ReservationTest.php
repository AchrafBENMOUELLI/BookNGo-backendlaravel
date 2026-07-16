<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\WithFixtures;

class ReservationTest extends TestCase
{
    use RefreshDatabase;
    use WithFixtures;

    private User $user;
    private string $token;
    private Hotel $hotel;

    protected function setUp(): void
        {
            parent::setUp();

            Http::fake([
                '*' => Http::response(['message' => 'ok'], 200),
            ]);

            $this->user  = User::factory()->create();
            $this->token = $this->user->createToken('test')->plainTextToken;
            $this->hotel = Hotel::factory()->create(['prix_unitaire' => 200]);
        }

    private function authHeader(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_authenticated_user_can_create_reservation(): void
    {
        $payload = $this->loadFixture('reservations.create_valid');
        $payload['id_user'] = $this->user->id;
        $payload['id_hotel'] = $this->hotel->id;

        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/reservations', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reservations', [
            'id_hotel' => $this->hotel->id,
            'id_user'  => $this->user->id,
        ]);
    }

    public function test_cannot_reserve_with_overlapping_dates(): void
    {
        $overlapFirst = $this->loadFixture('reservations.overlap_first');
        Reservation::factory()->create([
            'id_hotel'     => $this->hotel->id,
            'id_user'      => $this->user->id,
            'date_arrivee' => $overlapFirst['date_arrivee'],
            'date_depart'  => $overlapFirst['date_depart'],
            'etat'         => 'en_attente',
        ]);

        $payload = $this->loadFixture('reservations.overlap_second');
        $payload['id_user'] = $this->user->id;
        $payload['id_hotel'] = $this->hotel->id;

        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/reservations', $payload);

        $response->assertStatus(422);
    }

    public function test_user_can_get_their_reservations(): void
    {
        Reservation::factory()->count(3)->create([
            'id_user'  => $this->user->id,
            'id_hotel' => $this->hotel->id,
        ]);

        $response = $this->withHeaders($this->authHeader())
                         ->getJson("/api/reservations?id_user={$this->user->id}");

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_user_can_cancel_reservation(): void
    {
        $reservation = Reservation::factory()->create([
            'id_user'  => $this->user->id,
            'id_hotel' => $this->hotel->id,
            'etat'     => 'en_attente',
        ]);

        $response = $this->withHeaders($this->authHeader())
                         ->patchJson("/api/reservations/{$reservation->id}", $this->loadFixture('reservations.cancel_update'));

        $response->assertStatus(200);
        $this->assertDatabaseHas('reservations', [
            'id'   => $reservation->id,
            'etat' => 'annulee',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_reservation(): void
    {
        $response = $this->postJson('/api/reservations', [
            'id_hotel' => $this->hotel->id,
        ]);
        $response->assertStatus(401);
    }

    public function test_cannot_create_reservation_without_required_fields(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/reservations', $this->loadFixture('reservations.create_empty'));
        $response->assertStatus(422);
    }

    public function test_cannot_create_reservation_with_past_date(): void
    {
        $payload = $this->loadFixture('reservations.past_dates');
        $payload['id_user'] = $this->user->id;
        $payload['id_hotel'] = $this->hotel->id;

        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/reservations', $payload);
        $response->assertStatus(422);
    }

    public function test_cannot_create_reservation_with_invalid_hotel(): void
    {
        $payload = $this->loadFixture('reservations.invalid_hotel');
        $payload['id_user'] = $this->user->id;

        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/reservations', $payload);
        $response->assertStatus(422);
    }

    public function test_cannot_show_nonexistent_reservation(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->getJson('/api/reservations/99999');
        $response->assertStatus(404);
    }

    public function test_cannot_update_nonexistent_reservation(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->patchJson('/api/reservations/99999', ['etat' => 'confirmee']);
        $response->assertStatus(404);
    }

    public function test_cannot_delete_nonexistent_reservation(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->deleteJson('/api/reservations/99999');
        $response->assertStatus(404);
    }
}
