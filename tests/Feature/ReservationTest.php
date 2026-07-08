<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;
    private Hotel $hotel;

    protected function setUp(): void
        {
            parent::setUp();

            Http::fake([
                '*' => Http::response(['message' => 'ok'], 200), // ← fake ALL http calls
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
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/reservations', [
                             'id_user'        => $this->user->id,
                             'id_hotel'       => $this->hotel->id,
                             'date_arrivee'   => '2027-01-10',
                             'date_depart'    => '2027-01-15',
                             'nombre_adultes' => 2,
                             'nombre_enfants' => 0,
                             'nbr_chambre'    => 1,
                             'prix'           => 1000,
                             'etat'           => 'en_attente',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reservations', [
            'id_hotel' => $this->hotel->id,
            'id_user'  => $this->user->id,
        ]);
    }

    public function test_cannot_reserve_with_overlapping_dates(): void
    {
        Reservation::factory()->create([
            'id_hotel'     => $this->hotel->id,
            'id_user'      => $this->user->id,
            'date_arrivee' => '2027-01-10',
            'date_depart'  => '2027-01-15',
            'etat'         => 'en_attente',
        ]);

        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/reservations', [
                             'id_user'        => $this->user->id,
                             'id_hotel'       => $this->hotel->id,
                             'date_arrivee'   => '2027-01-12',
                             'date_depart'    => '2027-01-17',
                             'nombre_adultes' => 2,
                             'nombre_enfants' => 0,
                             'nbr_chambre'    => 1,
                             'prix'           => 1000,
                         ]);

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
                         ->patchJson("/api/reservations/{$reservation->id}", [
                             'etat' => 'annulee',
                         ]);

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
                         ->postJson('/api/reservations', []);
        $response->assertStatus(422);
    }

    public function test_cannot_create_reservation_with_past_date(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/reservations', [
                             'id_user'        => $this->user->id,
                             'id_hotel'       => $this->hotel->id,
                             'date_arrivee'   => '2020-01-10',
                             'date_depart'    => '2020-01-15',
                             'nombre_adultes' => 2,
                             'nbr_chambre'    => 1,
                             'prix'           => 1000,
                         ]);
        $response->assertStatus(422);
    }

    public function test_cannot_create_reservation_with_invalid_hotel(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/reservations', [
                             'id_user'        => $this->user->id,
                             'id_hotel'       => 99999,
                             'date_arrivee'   => '2027-06-10',
                             'date_depart'    => '2027-06-15',
                             'nombre_adultes' => 2,
                             'nbr_chambre'    => 1,
                             'prix'           => 1000,
                         ]);
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
