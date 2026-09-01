<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affectation extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'direction_id',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }
}
