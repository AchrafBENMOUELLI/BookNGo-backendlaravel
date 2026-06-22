<?php

namespace App\Services;

use App\Models\FormuleTarif;
use Illuminate\Database\Eloquent\Collection;

class FormuleTarifService
{
    public function getAll(): Collection
    {
        return FormuleTarif::with('hotel')->get();
    }

    public function find(int $id): FormuleTarif
    {
        return FormuleTarif::with('hotel')->findOrFail($id);
    }

    public function create(array $data): FormuleTarif
    {
        return FormuleTarif::create($data);
    }

    public function update(int $id, array $data): FormuleTarif
    {
        $formule = FormuleTarif::findOrFail($id);
        $formule->update($data);
        return $formule;
    }

    public function delete(int $id): void
    {
        $formule = FormuleTarif::findOrFail($id);
        $formule->delete();
    }
}
