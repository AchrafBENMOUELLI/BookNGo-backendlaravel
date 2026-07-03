<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReservationService
{
    public function getAll(array $filters = [])
{
    $query = Reservation::with(['user', 'hotel']);

    if (!empty($filters['id_user'])) {
        $query->where('id_user', $filters['id_user']);
    }

    return $query->get();
}

    public function find(int $id): Reservation
    {
        return Reservation::with(['user', 'hotel'])->findOrFail($id);
    }

    public function create(array $data): Reservation
{
    $this->checkAvailability($data['id_hotel'], $data['date_arrivee'], $data['date_depart']);

    $reservation = Reservation::create($data);

    // Load relations for the webhook
    $reservation->load(['user', 'hotel']);

    // Trigger n8n workflow
    Http::post(config('services.n8n.webhook_url'), [
        'user_name'    => $reservation->user->name,
        'email'        => $reservation->user->email,
        'hotel_nom'    => $reservation->hotel->nom,
        'date_arrivee' => $reservation->date_arrivee,
        'date_depart'  => $reservation->date_depart,
        'nbr_chambre'  => $reservation->nbr_chambre,
        'prix'         => $reservation->prix,
    ]);

    return $reservation;
}

  public function update(int $id, array $data): Reservation
{
    $reservation = Reservation::findOrFail($id);

    if (isset($data['date_arrivee']) || isset($data['date_depart'])) {
        $this->checkAvailability(
            $data['id_hotel'] ?? $reservation->id_hotel,
            $data['date_arrivee'] ?? $reservation->date_arrivee,
            $data['date_depart'] ?? $reservation->date_depart,
            excludeReservationId: $id
        );
    }

    $reservation->update($data);

    if (isset($data['etat'])) {
        $reservation->load(['user', 'hotel']);

        Log::info('Status changed to: ' . $data['etat']);
        Log::info('Calling webhook: ' . config('services.n8n.status_webhook_url'));

        $response = Http::post(config('services.n8n.status_webhook_url'), [
            'user_name'    => $reservation->user->name,
            'email'        => $reservation->user->email,
            'hotel_nom'    => $reservation->hotel->nom,
            'date_arrivee' => $reservation->date_arrivee,
            'date_depart'  => $reservation->date_depart,
            'etat'         => $reservation->etat,
            'prix'         => $reservation->prix,
        ]);

        Log::info('Webhook response: ' . $response->status());
    }

    return $reservation;
}

    public function delete(int $id): void
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
    }

    /**
     * Vérifie qu'il n'y a pas de chevauchement de dates pour cet hôtel.
     * (Logique simple à affiner plus tard selon nb de chambres dispo)
     */
    private function checkAvailability(int $hotelId, string $dateArrivee, string $dateDepart, ?int $excludeReservationId = null): void
    {
        $query = Reservation::where('id_hotel', $hotelId)
            ->where('etat', '!=', 'annulee')
            ->where(function ($q) use ($dateArrivee, $dateDepart) {
                $q->whereBetween('date_arrivee', [$dateArrivee, $dateDepart])
                  ->orWhereBetween('date_depart', [$dateArrivee, $dateDepart])
                  ->orWhere(function ($q2) use ($dateArrivee, $dateDepart) {
                      $q2->where('date_arrivee', '<=', $dateArrivee)
                         ->where('date_depart', '>=', $dateDepart);
                  });
            });

        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'date_arrivee' => 'Cet hôtel n\'est pas disponible pour ces dates.',
            ]);
        }
    }
}
