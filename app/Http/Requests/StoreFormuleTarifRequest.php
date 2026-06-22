<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormuleTarifRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_id' => 'required|exists:hotels,id',
            'formule' => 'required|string|max:100',
            'type_chambre' => 'nullable|string|max:100',
            'prix_chambre' => 'nullable|numeric|min:0',
            'prix_formule' => 'required|numeric|min:0',
            'promotion' => 'nullable|numeric|min:0|max:100',
            'periode_debut' => 'nullable|date',
            'periode_fin' => 'nullable|date|after_or_equal:periode_debut',
        ];
    }
}
