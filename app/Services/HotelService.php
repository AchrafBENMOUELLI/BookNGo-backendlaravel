<?php

namespace App\Services;

use App\Models\Hotel;

class HotelService
{
    public function getAll(array $filters = [])
    {
        $query = Hotel::query();

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nom', 'like', "%{$filters['search']}%")
                  ->orWhere('adresse', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['categorie'])) {
            $query->where('categorie', $filters['categorie']);
        }

        $perPage = $filters['per_page'] ?? 9;

        return $query->paginate($perPage);
    }

    public function find(int $id): Hotel
    {
        return Hotel::with('formules')->findOrFail($id);
    }

    public function create(array $data): Hotel
    {
        return Hotel::create($data);
    }

    public function update(int $id, array $data): Hotel
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update($data);
        return $hotel;
    }

    public function delete(int $id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();
    }
}
