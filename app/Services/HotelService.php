<?php

namespace App\Services;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Collection;

class HotelService
{
    public function getAll(): Collection
    {
        return Hotel::with('formules')->get();
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
