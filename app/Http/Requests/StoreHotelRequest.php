<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // on gère l'auth plus tard avec un middleware
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'categorie' => 'nullable|string|max:50',
            'adresse' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'photos' => 'nullable|array',
            'photos.*' => 'string', // chaque élément du tableau = une URL/path
            'prix_unitaire' => 'nullable|numeric|min:0',
        ];
    }
}
