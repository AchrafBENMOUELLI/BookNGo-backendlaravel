<?php

namespace Tests\Unit;

use App\Models\FormuleTarif;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormuleTarifTest extends TestCase
{
    use RefreshDatabase;

    public function test_prix_avec_promotion_is_calculated_correctly(): void
    {
        $formule = new FormuleTarif([
            'prix_formule' => 100,
            'promotion'    => 20,
        ]);

        $this->assertEquals(80, $formule->prix_avec_promotion);
    }

    public function test_prix_avec_promotion_returns_original_when_no_promotion(): void
    {
        $formule = new FormuleTarif([
            'prix_formule' => 100,
            'promotion'    => 0,
        ]);

        $this->assertEquals(100, $formule->prix_avec_promotion);
    }

    public function test_duree_periode_is_calculated_correctly(): void
    {
        $formule = FormuleTarif::factory()->create([
            'periode_debut' => '2027-01-01',
            'periode_fin'   => '2027-01-10',
            'prix_formule'  => 100,
            'promotion'     => 0,
        ]);

        $this->assertEquals(10, $formule->duree_periode);
    }
}
