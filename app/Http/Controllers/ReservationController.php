<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Services\ReservationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(private ReservationService $reservationService) {}

    public function index(Request $request)
    {
        $reservations = $this->reservationService->getAll($request->only(['id_user']));
        return ReservationResource::collection($reservations);
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
