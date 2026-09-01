<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleDroit extends Model
{
    use HasFactory;

    protected $table = 'role_droits';

    protected $fillable = [
        'role_id',
        'droit_id',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function droit()
    {
        return $this->belongsTo(Droit::class);
    }
}
