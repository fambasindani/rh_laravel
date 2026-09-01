<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fonction extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'statut',
    ];

    protected $casts = [
        'statut' => 'boolean',
    ];

    public function agents() { return $this->hasMany(Agent::class); }
}
