<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conge extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nombre_jours',
        'motif',
        'statut',
        'date_demande',
        'date_validation',
        'validateur',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_demande' => 'date',
        'date_validation' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function typeConge()
    {
        return $this->belongsTo(TypeConge::class);
    }
}
