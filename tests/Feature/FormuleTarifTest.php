<?php

namespace Tests\Feature;

use App\Models\FormuleTarif;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\WithFixtures;

class FormuleTarifTest extends TestCase
{
    use RefreshDatabase;
    use WithFixtures;

    private User $user;
    private string $token;
    private Hotel $hotel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
        $this->hotel = Hotel::factory()->create();
    }

    private function authHeader(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_list_formules(): void
    {
        FormuleTarif::factory()->count(3)->create();

        $response = $this->withHeaders($this->authHeader())
                         ->getJson('/api/formules-tarifs');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_formule(): void
    {
        $payload = $this->loadFixture('formules.create_valid');
        $payload['hotel_id'] = $this->hotel->id;

        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/formules-tarifs', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['formule' => 'Demi Pension']);
        $this->assertDatabaseHas('formules_tarifs', ['formule' => 'Demi Pension']);
    }

    public function test_can_show_formule(): void
    {
        $formule = FormuleTarif::factory()->create();

        $response = $this->withHeaders($this->authHeader())
                         ->getJson("/api/formules-tarifs/{$formule->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $formule->id]);
    }

    public function test_can_update_formule(): void
    {
        $formule = FormuleTarif::factory()->create();

        $response = $this->withHeaders($this->authHeader())
                         ->putJson("/api/formules-tarifs/{$formule->id}", $this->loadFixture('formules.update_valid'));

        $response->assertStatus(200);
        $this->assertDatabaseHas('formules_tarifs', [
            'id'           => $formule->id,
            'prix_formule' => 500,
            'promotion'    => 20,
        ]);
    }

    public function test_can_delete_formule(): void
    {
        $formule = FormuleTarif::factory()->create();

        $response = $this->withHeaders($this->authHeader())
                         ->deleteJson("/api/formules-tarifs/{$formule->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('formules_tarifs', ['id' => $formule->id]);
    }

    public function test_unauthenticated_user_cannot_access_formules(): void
    {
        $response = $this->getJson('/api/formules-tarifs');
        $response->assertStatus(401);
    }

    public function test_cannot_create_formule_without_required_fields(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/formules-tarifs', $this->loadFixture('formules.create_missing_fields'));
        $response->assertStatus(422);
    }

    public function test_cannot_create_formule_with_invalid_hotel(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->postJson('/api/formules-tarifs', $this->loadFixture('formules.create_invalid_hotel'));
        $response->assertStatus(422);
    }

    public function test_cannot_show_nonexistent_formule(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->getJson('/api/formules-tarifs/99999');
        $response->assertStatus(404);
    }

    public function test_cannot_update_nonexistent_formule(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->putJson('/api/formules-tarifs/99999', $this->loadFixture('formules.update_nonexistent'));
        $response->assertStatus(404);
    }

    public function test_cannot_delete_nonexistent_formule(): void
    {
        $response = $this->withHeaders($this->authHeader())
                         ->deleteJson('/api/formules-tarifs/99999');
        $response->assertStatus(404);
    }

    public function test_prix_avec_promotion_is_calculated_correctly(): void
    {
        $data = $this->loadFixture('formules.promotion_calculation');
        $formule = new FormuleTarif($data);

        $this->assertEquals(80, $formule->prix_avec_promotion);
    }

    public function test_prix_avec_promotion_returns_original_when_no_promotion(): void
    {
        $data = $this->loadFixture('formules.no_promotion');
        $formule = new FormuleTarif($data);

        $this->assertEquals(100, $formule->prix_avec_promotion);
    }

    public function test_duree_periode_is_calculated_correctly(): void
    {
        $data = $this->loadFixture('formules.duree_periode');
        $formule = FormuleTarif::factory()->create($data);

        $this->assertEquals(10, $formule->duree_periode);
    }
}
