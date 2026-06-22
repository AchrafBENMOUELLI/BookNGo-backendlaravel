<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_hotel',
        'date_arrivee',
        'date_depart',
        'nombre_adultes',
        'nombre_enfants',
        'etat',
        'formule',
        'prix',
        'nbr_chambre',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'id_hotel');
    }
}
