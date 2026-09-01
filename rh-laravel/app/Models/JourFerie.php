<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JourFerie extends Model
{
    use HasFactory;

    protected $table = 'jours_feries';

    protected $fillable = [
        'nom',
        'date',
        'actif',
    ];

    protected $casts = [
        'date' => 'date',
        'actif' => 'boolean',
    ];
}
