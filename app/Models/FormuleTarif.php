<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormuleTarif extends Model
{
    use HasFactory;

    protected $table = 'formules_tarifs';

    protected $fillable = [
        'hotel_id',
        'formule',
        'type_chambre',
        'prix_chambre',
        'prix_formule',
        'promotion',
        'periode_debut',
        'periode_fin',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function getPrixAvecPromotionAttribute()
    {
        if ($this->promotion > 0) {
            return $this->prix_formule * (1 - $this->promotion / 100);
        }

        return $this->prix_formule;
    }

    public function getDureePeriodeAttribute()
    {
        if ($this->periode_debut && $this->periode_fin) {
            return $this->periode_debut->diffInDays($this->periode_fin) + 1;
        }

        return 0;
    }
}
