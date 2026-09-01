<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prime extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'libelle',
        'montant',
        'date_prime',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_prime' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
