<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'grade_id',
        'date_debut',
        'date_fin',
        'reference',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
