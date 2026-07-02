<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FormuleTarifController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes publiques (lecture seule pour les utilisateurs normaux)
Route::get('/hotels', [HotelController::class, 'index']);
Route::get('/hotels/{hotel}', [HotelController::class, 'show']);

// Routes protégées (token requis)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('formules-tarifs', FormuleTarifController::class);
    Route::apiResource('reservations', ReservationController::class);

    // Routes admin uniquement
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);

        Route::apiResource('hotels', HotelController::class)->except(['index', 'show']);
        Route::apiResource('users', UserController::class)->except(['show']);

        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::patch('/reservations/{reservation}', [ReservationController::class, 'update']);
        Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);
    });
});
