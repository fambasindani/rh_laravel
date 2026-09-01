<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affiliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'nom',
        'postnom',
        'prenom',
        'date_naissance',
        'lieu_naissance',
        'relation',
        'etat',
        'statut',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
