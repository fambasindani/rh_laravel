<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'message',
        'lu',
        'date_notification',
    ];

    protected $casts = [
        'lu' => 'boolean',
        'date_notification' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
