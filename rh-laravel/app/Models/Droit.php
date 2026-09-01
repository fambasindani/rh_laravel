<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Droit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_droit',
        'module',
        'description',
        'date_creation',
    ];

    protected $appends = ['nom'];

    public function getNomAttribute()
    {
        return $this->nom_droit;
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_droits');
    }
}
