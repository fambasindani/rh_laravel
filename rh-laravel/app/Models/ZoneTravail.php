<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneTravail extends Model
{
    use HasFactory;

    protected $table = 'zones_travail';

    protected $fillable = [
        'nom',
        'adresse',
        'latitude',
        'longitude',
        'rayon',
        'actif',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'rayon' => 'integer',
        'actif' => 'boolean',
    ];

    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }
}
