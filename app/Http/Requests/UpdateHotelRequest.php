<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => 'sometimes|required|string|max:255',
            'categorie' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'photos' => 'nullable|array',
            'photos.*' => 'string',
            'prix_unitaire' => 'nullable|numeric|min:0',
        ];
    }
}
