<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'date_permission',
        'heure_sortie',
        'heure_retour',
        'motif',
        'statut',
    ];

    protected $casts = [
        'date_permission' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
