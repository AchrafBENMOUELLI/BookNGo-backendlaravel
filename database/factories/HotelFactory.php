<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom'           => $this->faker->company() . ' Hotel',
            'categorie'     => (string) $this->faker->numberBetween(1, 5),
            'adresse'       => $this->faker->address(),
            'email'         => $this->faker->companyEmail(),
            'photos'        => null,
            'prix_unitaire' => $this->faker->randomFloat(2, 50, 500),
        ];
    }
}
