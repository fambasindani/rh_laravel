<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'agent_id',
        'username',
        'password_hash',
        'actif',
        'last_login',
        'date_creation',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'last_login' => 'datetime',
        'date_creation' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function historiqueConnexions()
    {
        return $this->hasMany(HistoriqueConnexion::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
