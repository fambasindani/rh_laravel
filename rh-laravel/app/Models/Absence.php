<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'date_debut',
        'date_fin',
        'motif',
        'statut',
        'justification',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
