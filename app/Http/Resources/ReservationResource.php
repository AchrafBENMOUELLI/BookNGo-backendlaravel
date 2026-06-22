<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date_arrivee' => $this->date_arrivee,
            'date_depart' => $this->date_depart,
            'nombre_adultes' => $this->nombre_adultes,
            'nombre_enfants' => $this->nombre_enfants,
            'etat' => $this->etat,
            'formule' => $this->formule,
            'prix' => $this->prix,
            'nbr_chambre' => $this->nbr_chambre,
            'user' => new UserResource($this->whenLoaded('user')),
            'hotel' => new HotelResource($this->whenLoaded('hotel')),
            'created_at' => $this->created_at,
        ];
    }
}
