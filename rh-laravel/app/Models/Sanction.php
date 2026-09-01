<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'type_sanction',
        'motif',
        'date_sanction',
        'reference',
    ];

    protected $casts = [
        'date_sanction' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
