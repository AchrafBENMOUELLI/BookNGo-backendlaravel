<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_user' => 'sometimes|required|exists:users,id',
            'id_hotel' => 'sometimes|required|exists:hotels,id',
            'date_arrivee' => 'sometimes|required|date',
            'date_depart' => 'sometimes|required|date|after:date_arrivee',
            'nombre_adultes' => 'sometimes|required|integer|min:1',
            'nombre_enfants' => 'nullable|integer|min:0',
            'etat' => 'nullable|string|in:en_attente,confirmee,annulee',
            'formule' => 'nullable|string|max:100',
            'prix' => 'sometimes|required|numeric|min:0',
            'nbr_chambre' => 'sometimes|required|integer|min:1',
        ];
    }
}
