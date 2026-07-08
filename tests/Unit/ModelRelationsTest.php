<?php

namespace Tests\Unit;

use App\Models\FormuleTarif;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_hotel_has_many_reservations(): void
    {
        $hotel = Hotel::factory()->create();
        Reservation::factory()->count(3)->create(['id_hotel' => $hotel->id]);

        $this->assertInstanceOf(HasMany::class, $hotel->reservations());
        $this->assertCount(3, $hotel->reservations);
    }

    public function test_hotel_has_many_formules(): void
    {
        $hotel = Hotel::factory()->create();
        FormuleTarif::factory()->count(2)->create(['hotel_id' => $hotel->id]);

        $this->assertInstanceOf(HasMany::class, $hotel->formules());
        $this->assertCount(2, $hotel->formules);
    }

    public function test_reservation_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $reservation = Reservation::factory()->create([
            'id_user'  => $user->id,
            'id_hotel' => $hotel->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $reservation->user());
        $this->assertTrue($reservation->user->is($user));
    }

    public function test_reservation_belongs_to_hotel(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        $reservation = Reservation::factory()->create([
            'id_user'  => $user->id,
            'id_hotel' => $hotel->id,
        ]);

        $this->assertInstanceOf(BelongsTo::class, $reservation->hotel());
        $this->assertTrue($reservation->hotel->is($hotel));
    }

    public function test_user_has_many_reservations(): void
    {
        $user = User::factory()->create();
        $hotel = Hotel::factory()->create();
        Reservation::factory()->count(2)->create([
            'id_user'  => $user->id,
            'id_hotel' => $hotel->id,
        ]);

        $this->assertInstanceOf(HasMany::class, $user->reservations());
        $this->assertCount(2, $user->reservations);
    }

    public function test_formule_belongs_to_hotel(): void
    {
        $hotel = Hotel::factory()->create();
        $formule = FormuleTarif::factory()->create(['hotel_id' => $hotel->id]);

        $this->assertInstanceOf(BelongsTo::class, $formule->hotel());
        $this->assertTrue($formule->hotel->is($hotel));
    }

    public function test_hotel_casts_photos_as_array(): void
    {
        $photos = ['photo1.jpg', 'photo2.jpg'];
        $hotel = Hotel::factory()->create(['photos' => $photos]);

        $this->assertIsArray($hotel->photos);
        $this->assertEquals($photos, $hotel->photos);
    }

    public function test_hotel_casts_prix_unitaire_as_float(): void
    {
        $hotel = Hotel::factory()->create(['prix_unitaire' => 199.50]);

        $this->assertIsFloat($hotel->prix_unitaire);
        $this->assertEquals(199.50, $hotel->prix_unitaire);
    }

    public function test_user_has_default_role_user(): void
    {
        $user = User::factory()->create();

        $this->assertEquals('user', $user->role);
    }

    public function test_reservation_factory_creates_en_attente_by_default(): void
    {
        $reservation = Reservation::factory()->create();

        $this->assertEquals('en_attente', $reservation->etat);
    }
}
