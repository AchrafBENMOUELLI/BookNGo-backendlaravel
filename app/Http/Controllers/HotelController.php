<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Http\Resources\HotelResource;
use App\Services\HotelService;

class HotelController extends Controller
{
    public function __construct(private HotelService $hotelService) {}

    public function index()
    {
        return HotelResource::collection($this->hotelService->getAll());
    }

    public function store(StoreHotelRequest $request)
    {
        $hotel = $this->hotelService->create($request->validated());
        return new HotelResource($hotel);
    }

    public function show(int $id)
    {
        return new HotelResource($this->hotelService->find($id));
    }

    public function update(UpdateHotelRequest $request, int $id)
    {
        $hotel = $this->hotelService->update($id, $request->validated());
        return new HotelResource($hotel);
    }

    public function destroy(int $id)
    {
        $this->hotelService->delete($id);
        return response()->json(['message' => 'Hôtel supprimé avec succès.']);
    }
}
