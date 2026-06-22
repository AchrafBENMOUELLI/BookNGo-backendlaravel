<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'categorie',
        'adresse',
        'email',
        'photos',
        'prix_unitaire',
    ];

    protected $casts = [
        'photos'        => 'array',
        'prix_unitaire' => 'float',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'id_hotel');
    }

    public function formules()
    {
        return $this->hasMany(FormuleTarif::class);
    }
}
