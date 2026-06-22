<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_user' => 'required|exists:users,id',
            'id_hotel' => 'required|exists:hotels,id',
            'date_arrivee' => 'required|date|after_or_equal:today',
            'date_depart' => 'required|date|after:date_arrivee',
            'nombre_adultes' => 'required|integer|min:1',
            'nombre_enfants' => 'nullable|integer|min:0',
            'etat' => 'nullable|string|in:en_attente,confirmee,annulee',
            'formule' => 'nullable|string|max:100',
            'prix' => 'required|numeric|min:0',
            'nbr_chambre' => 'required|integer|min:1',
        ];
    }
}
