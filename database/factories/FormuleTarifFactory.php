<?php

namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormuleTarifFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_id'      => Hotel::factory(),
            'formule'       => $this->faker->randomElement(['Petit Déjeuner', 'Demi Pension', 'Tout Inclus']),
            'type_chambre'  => $this->faker->randomElement(['Standard', 'Supérieure', 'Suite']),
            'prix_chambre'  => $this->faker->randomFloat(2, 50, 300),
            'prix_formule'  => $this->faker->randomFloat(2, 100, 500),
            'promotion'     => 0,
            'periode_debut' => '2027-01-01',
            'periode_fin'   => '2027-12-31',
        ];
    }
}
