<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'intitule',
        'description',
        'date_debut',
        'date_fin',
        'lieu',
        'organisme',
        'statut',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function agentFormations()
    {
        return $this->hasMany(AgentFormation::class);
    }
}
