<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    protected $casts = [
        'periode_debut' => 'date',
        'periode_fin'   => 'date',
        'prix_chambre'  => 'float',
        'prix_formule'  => 'float',
        'promotion'     => 'float',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function getPrixAvecPromotionAttribute()
    {
        if ($this->promotion > 0) {
            return round($this->prix_formule * (1 - $this->promotion / 100), 2);
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
