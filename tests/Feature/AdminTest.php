<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private string $token;

    protected function setUp(): void
{
    parent::setUp();

    Http::fake([
        '*' => Http::response(['message' => 'ok'], 200), // ← fake ALL http calls
    ]);

    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->token = $this->admin->createToken('test')->plainTextToken;
}

    private function authHeader(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    // AdminController
    public function test_admin_can_get_stats(): void
    {
        Hotel::factory()->count(3)->create();

        $response = $this->withHeaders($this->authHeader())
                         ->getJson('/api/admin/stats');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'total_hotels',
                     'total_reservations',
                     'total_users',
                     'revenue_total',
                     'reservations_en_attente',
                     'reservations_confirmees',
                     'reservations_annulees',
                     'recent_reservations',
                 ]);
    }

    public function test_non_admin_cannot_access_stats(): void
    {
        $user  = User::factory()->create(['role' => 'user']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->getJson('/api/admin/stats');

        $response->assertStatus(403);
    }

    // UserController
    public function test_admin_can_get_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->withHeaders($this->authHeader())
                         ->getJson('/api/admin/users');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_user(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/admin/users', [
                             'name'     => 'New User',
                             'email'    => 'newuser@test.com',
                             'password' => 'password123',
                             'role'     => 'user',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);
    }

    public function test_admin_can_create_another_admin(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/admin/users', [
                             'name'     => 'New Admin',
                             'email'    => 'newadmin@test.com',
                             'password' => 'password123',
                             'role'     => 'admin',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'newadmin@test.com', 'role' => 'admin']);
    }

    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->withHeaders($this->authHeader())
                         ->deleteJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    // Admin hotel management
    public function test_admin_can_update_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->withHeaders($this->authHeader())
                         ->putJson("/api/admin/hotels/{$hotel->id}", [
                             'nom'           => 'Updated Hotel',
                             'categorie'     => '5',
                             'adresse'       => 'New Address',
                             'prix_unitaire' => 300,
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('hotels', ['nom' => 'Updated Hotel']);
    }

    // Admin reservation management
    public function test_admin_can_get_all_reservations(): void
    {
        $user  = User::factory()->create();
        $hotel = Hotel::factory()->create();
        Reservation::factory()->count(3)->create([
            'id_user'  => $user->id,
            'id_hotel' => $hotel->id,
        ]);

        $response = $this->withHeaders($this->authHeader())
                         ->getJson('/api/admin/reservations');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    public function test_admin_can_update_reservation_status(): void
    {
        $user        = User::factory()->create();
        $hotel       = Hotel::factory()->create();
        $reservation = Reservation::factory()->create([
            'id_user'  => $user->id,
            'id_hotel' => $hotel->id,
            'etat'     => 'en_attente',
        ]);

        $response = $this->withHeaders($this->authHeader())
                         ->patchJson("/api/admin/reservations/{$reservation->id}", [
                             'etat' => 'confirmee',
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reservations', [
            'id'   => $reservation->id,
            'etat' => 'confirmee',
        ]);
    }

    public function test_admin_can_delete_reservation(): void
    {
        $user        = User::factory()->create();
        $hotel       = Hotel::factory()->create();
        $reservation = Reservation::factory()->create([
            'id_user'  => $user->id,
            'id_hotel' => $hotel->id,
        ]);

        $response = $this->withHeaders($this->authHeader())
                         ->deleteJson("/api/admin/reservations/{$reservation->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create(['name' => 'Original Name', 'role' => 'user']);

        $response = $this->withHeaders($this->authHeader())
                         ->putJson("/api/admin/users/{$user->id}", [
                             'name' => 'Updated Name',
                             'role' => 'admin',
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'name' => 'Updated Name',
            'role' => 'admin',
        ]);
    }

    public function test_non_admin_cannot_update_user(): void
    {
        $adminUser = User::factory()->create(['role' => 'user']);
        $token     = $adminUser->createToken('test')->plainTextToken;

        $target = User::factory()->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                         ->putJson("/api/admin/users/{$target->id}", [
                             'name' => 'Hacked Name',
                         ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_create_user_without_name(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/admin/users', [
                             'email'    => 'test@test.com',
                             'password' => 'password123',
                             'role'     => 'user',
                         ]);
        $response->assertStatus(422);
    }

    public function test_admin_cannot_create_user_with_short_password(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/admin/users', [
                             'name'     => 'Test',
                             'email'    => 'test@test.com',
                             'password' => '1234567',
                             'role'     => 'user',
                         ]);
        $response->assertStatus(422);
    }

    public function test_admin_cannot_create_user_with_invalid_role(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/admin/users', [
                             'name'     => 'Test',
                             'email'    => 'test@test.com',
                             'password' => 'password123',
                             'role'     => 'superadmin',
                         ]);
        $response->assertStatus(422);
    }

    public function test_admin_cannot_delete_nonexistent_user(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->deleteJson('/api/admin/users/99999');
        $response->assertStatus(404);
    }

    public function test_admin_cannot_update_nonexistent_user(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->putJson('/api/admin/users/99999', ['name' => 'Ghost']);
        $response->assertStatus(404);
    }

    public function test_admin_cannot_update_nonexistent_hotel(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->putJson('/api/admin/hotels/99999', ['nom' => 'Ghost']);
        $response->assertStatus(404);
    }

    public function test_admin_cannot_delete_nonexistent_hotel(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->deleteJson('/api/admin/hotels/99999');
        $response->assertStatus(404);
    }

    public function test_admin_cannot_update_nonexistent_reservation(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->patchJson('/api/admin/reservations/99999', ['etat' => 'confirmee']);
        $response->assertStatus(404);
    }

    public function test_admin_cannot_delete_nonexistent_reservation(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->deleteJson('/api/admin/reservations/99999');
        $response->assertStatus(404);
    }
}
