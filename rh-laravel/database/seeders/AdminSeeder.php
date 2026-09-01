<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Fonction;
use App\Models\Direction;
use App\Models\Agent;
use App\Models\User;
use App\Models\Role;
use App\Models\Droit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Grade
        $grade = Grade::firstOrCreate(
            ['sigle' => 'ADM'],
            ['nom' => 'Administrateur', 'statut' => true]
        );

        // Fonction
        $fonction = Fonction::firstOrCreate(
            ['nom' => 'Directeur General'],
            ['statut' => true]
        );

        // Direction
        $direction = Direction::firstOrCreate(
            ['sigle' => 'DG'],
            ['nom' => 'Direction Generale', 'statut' => true]
        );

        // Agent
        $agent = Agent::firstOrCreate(
            ['email' => 'pierrpapy@gmail.com'],
            [
                'matricule' => 'MAT001',
                'grade_id' => $grade->id,
                'fonction_id' => $fonction->id,
                'direction_id' => $direction->id,
                'nom' => 'Papy',
                'postnom' => 'Pierre',
                'prenom' => 'Admin',
                'sexe' => 'M',
                'date_naissance' => '1990-01-01',
                'telephone' => '+243800000000',
                'etat_civil' => 'Celibataire',
                'statut' => true,
                'reference_engagement' => 'REF001',
                'date_engagement' => '2024-01-01',
                'province' => 'Kinshasa',
                'territoire' => 'Lingwala',
                'village' => 'Kinshasa',
            ]
        );

        // Role ADMIN
        $role = Role::firstOrCreate(
            ['nom_role' => 'ADMIN'],
            ['description' => 'Administrateur avec tous les droits', 'date_creation' => now()]
        );

        // All Droits
        $modules = [
            'dashboard', 'agents', 'grades', 'fonctions', 'directions',
            'conges', 'absences', 'permissions', 'presences', 'pointages',
            'sanctions', 'formations', 'missions', 'evaluations', 'primes',
            'retraites', 'contrats', 'documents', 'notifications', 'roles',
            'users', 'droits', 'logs', 'configuration',
        ];

        foreach ($modules as $mod) {
            $droit = Droit::firstOrCreate(
                ['nom_droit' => 'ALL_' . strtoupper($mod)],
                ['description' => "Tous droits sur $mod", 'module' => $mod, 'date_creation' => now()]
            );
            if (!$role->droits()->where('droit_id', $droit->id)->exists()) {
                $role->droits()->attach($droit->id, ['date_attribution' => now()]);
            }
        }

        // User
        $user = User::firstOrCreate(
            ['username' => 'pierrpapy@gmail.com'],
            [
                'agent_id' => $agent->id,
                'password_hash' => Hash::make('12345678'),
                'actif' => true,
                'date_creation' => now(),
            ]
        );

        if (!$user->roles()->where('role_id', $role->id)->exists()) {
            $user->roles()->attach($role->id, ['date_attribution' => now()]);
        }

        $this->command->info('Admin user created: pierrpapy@gmail.com / 12345678');
    }
}
