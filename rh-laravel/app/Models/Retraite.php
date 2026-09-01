<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retraite extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'date_retraite',
        'reference',
        'observation',
    ];

    protected $casts = [
        'date_retraite' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
