<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueConnexion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'date_connexion',
        'deconnexion',
        'statut',
    ];

    protected $casts = [
        'date_connexion' => 'datetime',
        'deconnexion' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
