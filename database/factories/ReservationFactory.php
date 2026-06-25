<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id_user'        => User::factory(),
            'id_hotel'       => Hotel::factory(),
            'date_arrivee'   => '2027-03-01',
            'date_depart'    => '2027-03-05',
            'nombre_adultes' => $this->faker->numberBetween(1, 4),
            'nombre_enfants' => 0,
            'etat'           => 'en_attente',
            'formule'        => null,
            'prix'           => $this->faker->randomFloat(2, 100, 2000),
            'nbr_chambre'    => 1,
        ];
    }
}
