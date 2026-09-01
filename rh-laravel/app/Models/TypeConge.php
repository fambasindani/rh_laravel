<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeConge extends Model
{
    use HasFactory;

    protected $table = 'types_conges';

    protected $fillable = [
        'nom',
        'nombre_jours',
        'description',
        'statut',
    ];

    public function conges()
    {
        return $this->hasMany(Conge::class);
    }
}
