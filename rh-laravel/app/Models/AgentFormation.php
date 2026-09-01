<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentFormation extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'formation_id',
        'resultat',
        'observation',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}
