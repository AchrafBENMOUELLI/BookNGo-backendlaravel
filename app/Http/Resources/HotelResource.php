<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'categorie' => $this->categorie,
            'adresse' => $this->adresse,
            'email' => $this->email,
            'photos' => $this->photos,
            'prix_unitaire' => $this->prix_unitaire,
            'formules' => FormuleTarifResource::collection($this->whenLoaded('formules')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
