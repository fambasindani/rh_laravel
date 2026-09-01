<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'lieu',
        'motif',
        'date_depart',
        'date_retour',
        'reference',
    ];

    protected $casts = [
        'date_depart' => 'date',
        'date_retour' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
