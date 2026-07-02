<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\User;

class AdminController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_hotels'             => Hotel::count(),
            'total_reservations'       => Reservation::count(),
            'total_users'              => User::where('role', 'user')->count(),
            'total_admins'             => User::where('role', 'admin')->count(),
            'reservations_en_attente'  => Reservation::where('etat', 'en_attente')->count(),
            'reservations_confirmees'  => Reservation::where('etat', 'confirmee')->count(),
            'reservations_annulees'    => Reservation::where('etat', 'annulee')->count(),
            'revenue_total'            => Reservation::where('etat', '!=', 'annulee')->sum('prix'),
            'recent_reservations'      => Reservation::with(['user', 'hotel'])
                                              ->latest()
                                              ->take(5)
                                              ->get(),
        ]);
    }
}
