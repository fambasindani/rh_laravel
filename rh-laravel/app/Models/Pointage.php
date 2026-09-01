<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'type',
        'statut',
        'horodatage',
        'date_presence',
        'latitude',
        'longitude',
        'precision_gps',
        'chemin_photo',
        'infos_appareil',
        'id_appareil',
        'adresse_ip',
        'zone_travail_id',
        'motif_rejet',
        'justification',
        'minutes_retard',
    ];

    protected $casts = [
        'horodatage' => 'datetime',
        'date_presence' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'precision_gps' => 'float',
        'minutes_retard' => 'integer',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function zoneTravail()
    {
        return $this->belongsTo(ZoneTravail::class);
    }
}
