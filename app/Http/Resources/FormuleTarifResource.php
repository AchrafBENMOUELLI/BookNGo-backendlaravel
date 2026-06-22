<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormuleTarifResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hotel_id' => $this->hotel_id,
            'formule' => $this->formule,
            'type_chambre' => $this->type_chambre,
            'prix_chambre' => $this->prix_chambre,
            'prix_formule' => $this->prix_formule,
            'promotion' => $this->promotion,
            'prix_avec_promotion' => $this->prix_avec_promotion, // accessor du model
            'periode_debut' => $this->periode_debut,
            'periode_fin' => $this->periode_fin,
            'duree_periode' => $this->duree_periode, // accessor du model
            'hotel' => new HotelResource($this->whenLoaded('hotel')),
        ];
    }
}
