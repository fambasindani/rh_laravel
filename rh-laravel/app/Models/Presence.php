<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'date_presence',
        'heure_arrivee',
        'heure_depart',
        'statut',
        'observation',
    ];

    protected $casts = [
        'date_presence' => 'date',
        'retard_minutes' => 'integer',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
