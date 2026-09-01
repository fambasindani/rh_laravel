<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'sigle',
        'nom',
        'statut',
    ];

    protected $casts = [
        'statut' => 'boolean',
    ];

    public function agents() { return $this->hasMany(Agent::class); }
    public function promotions() { return $this->hasMany(Promotion::class); }
}
