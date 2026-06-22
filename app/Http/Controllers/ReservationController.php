<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Services\ReservationService;

class ReservationController extends Controller
{
    public function __construct(private ReservationService $reservationService) {}

    public function index()
    {
        return ReservationResource::collection($this->reservationService->getAll());
    }

    public function store(StoreReservationRequest $request)
    {
        $reservation = $this->reservationService->create($request->validated());
        return new ReservationResource($reservation);
    }

    public function show(int $id)
    {
        return new ReservationResource($this->reservationService->find($id));
    }

    public function update(UpdateReservationRequest $request, int $id)
    {
        $reservation = $this->reservationService->update($id, $request->validated());
        return new ReservationResource($reservation);
    }

    public function destroy(int $id)
    {
        $this->reservationService->delete($id);
        return response()->json(['message' => 'Réservation supprimée avec succès.']);
    }
}
