<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_role',
        'description',
        'date_creation',
    ];

    protected $appends = ['nom'];

    public function getNomAttribute()
    {
        return $this->nom_role;
    }

    public function droits()
    {
        return $this->belongsToMany(Droit::class, 'role_droits');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }
}
