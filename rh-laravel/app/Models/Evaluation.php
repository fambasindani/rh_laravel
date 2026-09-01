<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'date_evaluation',
        'note',
        'appreciation',
        'evaluateur',
    ];

    protected $casts = [
        'note' => 'float',
        'date_evaluation' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
