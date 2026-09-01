<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etude extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'nombre_annee',
        'lieu',
        'etablissement',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
