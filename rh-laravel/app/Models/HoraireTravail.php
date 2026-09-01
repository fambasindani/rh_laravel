<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoraireTravail extends Model
{
    use HasFactory;

    protected $table = 'horaires_travail';

    protected $fillable = [
        'agent_id',
        'jour_semaine',
        'heure_debut',
        'heure_fin',
        'debut_fenetre_pointage',
        'fin_fenetre_pointage',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
