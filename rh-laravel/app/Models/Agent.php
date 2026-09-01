<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'grade_id',
        'fonction_id',
        'direction_id',
        'nom',
        'postnom',
        'prenom',
        'sexe',
        'date_naissance',
        'email',
        'telephone',
        'etat_civil',
        'statut',
        'reference_engagement',
        'date_engagement',
        'province',
        'territoire',
        'village',
        'photo',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_engagement' => 'date',
        'statut' => 'boolean',
    ];

    public function grade() { return $this->belongsTo(Grade::class); }
    public function fonction() { return $this->belongsTo(Fonction::class); }
    public function direction() { return $this->belongsTo(Direction::class); }
    public function absences() { return $this->hasMany(Absence::class); }
    public function affectations() { return $this->hasMany(Affectation::class); }
    public function affiliations() { return $this->hasMany(Affiliation::class); }
    public function agentFormations() { return $this->hasMany(AgentFormation::class); }
    public function conges() { return $this->hasMany(Conge::class); }
    public function contrats() { return $this->hasMany(Contrat::class); }
    public function documents() { return $this->hasMany(Document::class); }
    public function etudes() { return $this->hasMany(Etude::class); }
    public function evaluations() { return $this->hasMany(Evaluation::class); }
    public function missions() { return $this->hasMany(Mission::class); }
    public function notifications() { return $this->hasMany(Notification::class); }
    public function permissions() { return $this->hasMany(Permission::class); }
    public function pointages() { return $this->hasMany(Pointage::class); }
    public function presences() { return $this->hasMany(Presence::class); }
    public function primes() { return $this->hasMany(Prime::class); }
    public function promotions() { return $this->hasMany(Promotion::class); }
    public function retraites() { return $this->hasMany(Retraite::class); }
    public function sanctions() { return $this->hasMany(Sanction::class); }
    public function user() { return $this->hasOne(User::class); }
}
