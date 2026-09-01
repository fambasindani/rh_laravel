<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Droit;
use App\Models\Role;
use Illuminate\Http\Request;

class DroitController extends Controller
{
    public function index(Request $request)
    {
        $query = Droit::with('roles');

        if ($request->has('keyword') && $request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nom_droit', 'like', "%{$keyword}%")
                  ->orWhere('module', 'like', "%{$keyword}%");
            });
        }

        $paginator = $query->orderBy('nom_droit')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($d) => [
            'id' => $d->id,
            'nomDroit' => $d->nom_droit,
            'description' => $d->description,
            'module' => $d->module,
            'dateCreation' => $d->date_creation ? (is_string($d->date_creation) ? substr($d->date_creation, 0, 10) : $d->date_creation->format('Y-m-d')) : null,
            'roles' => $d->roles,
        ]);

        return response()->json([
            'data' => $formatted,
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $nom = $data['nom'] ?? $data['nomDroit'] ?? null;

        if (!$nom) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                'nom' => ['Le champ nom est obligatoire.'],
            ]], 422);
        }

        $droit = Droit::create([
            'nom_droit' => $nom,
            'description' => $data['description'] ?? null,
            'module' => $data['module'] ?? null,
            'date_creation' => now(),
        ]);

        return response()->json([
            'id' => $droit->id,
            'nomDroit' => $droit->nom_droit,
            'description' => $droit->description,
            'module' => $droit->module,
            'dateCreation' => is_string($droit->date_creation) ? substr($droit->date_creation, 0, 10) : ($droit->date_creation?->format('Y-m-d')),
        ], 201);
    }

    public function show($id)
    {
        $d = Droit::with('roles')->findOrFail($id);
        return response()->json([
            'id' => $d->id,
            'nomDroit' => $d->nom_droit,
            'description' => $d->description,
            'module' => $d->module,
            'dateCreation' => $d->date_creation ? (is_string($d->date_creation) ? substr($d->date_creation, 0, 10) : $d->date_creation->format('Y-m-d')) : null,
            'roles' => $d->roles,
        ]);
    }

    public function update(Request $request, $id)
    {
        $droit = Droit::findOrFail($id);
        $data = $request->all();

        $droit->update([
            'nom_droit' => $data['nom'] ?? $data['nomDroit'] ?? $droit->nom_droit,
            'description' => $data['description'] ?? $droit->description,
            'module' => $data['module'] ?? $droit->module,
        ]);

        return response()->json([
            'id' => $droit->id,
            'nomDroit' => $droit->nom_droit,
            'description' => $droit->description,
            'module' => $droit->module,
            'dateCreation' => is_string($droit->date_creation) ? substr($droit->date_creation, 0, 10) : ($droit->date_creation?->format('Y-m-d')),
        ]);
    }

    public function destroy($id)
    {
        $droit = Droit::findOrFail($id);
        $droit->roles()->detach();
        $droit->delete();
        return response()->json(['message' => 'Droit supprimé avec succès']);
    }

    public function all()
    {
        $droits = Droit::orderBy('nom_droit')->get();
        return response()->json($droits->map(fn($d) => [
            'id' => $d->id,
            'nomDroit' => $d->nom_droit,
            'description' => $d->description,
            'module' => $d->module,
            'dateCreation' => $d->date_creation ? (is_string($d->date_creation) ? substr($d->date_creation, 0, 10) : $d->date_creation->format('Y-m-d')) : null,
        ]));
    }

    public function getByRoleId($roleId)
    {
        $role = Role::with('droits')->findOrFail($roleId);
        $droits = $role->droits;
        return response()->json($droits->map(fn($d) => [
            'id' => $d->id,
            'nomDroit' => $d->nom_droit,
            'description' => $d->description,
            'module' => $d->module,
            'dateCreation' => is_string($d->date_creation) ? substr($d->date_creation, 0, 10) : ($d->date_creation?->format('Y-m-d')),
        ]));
    }

    public function bulkAssignToRole(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $droitIds = is_array($request->input()) ? $request->input() : array_values($request->input());
        $role->droits()->syncWithPivotValues($droitIds, ['date_attribution' => now()]);
        return response()->json(['message' => 'Droits attribués avec succès']);
    }

    public function initDefaults()
    {
        $defaults = [
            ['nom_droit' => 'ALL_AGENTS', 'description' => 'Accès complet aux agents', 'module' => 'AGENTS'],
            ['nom_droit' => 'READ_AGENT', 'description' => 'Consulter un agent', 'module' => 'AGENTS'],
            ['nom_droit' => 'CREATE_AGENT', 'description' => 'Créer un agent', 'module' => 'AGENTS'],
            ['nom_droit' => 'UPDATE_AGENT', 'description' => 'Modifier un agent', 'module' => 'AGENTS'],
            ['nom_droit' => 'DELETE_AGENT', 'description' => 'Supprimer un agent', 'module' => 'AGENTS'],
            ['nom_droit' => 'ALL_ABSENCES', 'description' => 'Accès complet aux absences', 'module' => 'ABSENCES'],
            ['nom_droit' => 'READ_ABSENCE', 'description' => 'Consulter les absences', 'module' => 'ABSENCES'],
            ['nom_droit' => 'CREATE_ABSENCE', 'description' => 'Créer une absence', 'module' => 'ABSENCES'],
            ['nom_droit' => 'ALL_MISSIONS', 'description' => 'Accès complet aux missions', 'module' => 'MISSIONS'],
            ['nom_droit' => 'READ_MISSION', 'description' => 'Consulter les missions', 'module' => 'MISSIONS'],
            ['nom_droit' => 'ALL_EVALUATIONS', 'description' => 'Accès complet aux évaluations', 'module' => 'EVALUATIONS'],
            ['nom_droit' => 'READ_EVALUATION', 'description' => 'Consulter les évaluations', 'module' => 'EVALUATIONS'],
            ['nom_droit' => 'ALL_CONTRATS', 'description' => 'Accès complet aux contrats', 'module' => 'CONTRATS'],
            ['nom_droit' => 'READ_CONTRAT', 'description' => 'Consulter les contrats', 'module' => 'CONTRATS'],
            ['nom_droit' => 'ALL_PRIMES', 'description' => 'Accès complet aux primes', 'module' => 'PRIMES'],
            ['nom_droit' => 'READ_PRIME', 'description' => 'Consulter les primes', 'module' => 'PRIMES'],
            ['nom_droit' => 'ALL_PERMISSIONS', 'description' => 'Accès complet aux permissions', 'module' => 'PERMISSIONS'],
            ['nom_droit' => 'READ_PERMISSION', 'description' => 'Consulter les permissions', 'module' => 'PERMISSIONS'],
            ['nom_droit' => 'ALL_SANCTIONS', 'description' => 'Accès complet aux sanctions', 'module' => 'SANCTIONS'],
            ['nom_droit' => 'READ_SANCTION', 'description' => 'Consulter les sanctions', 'module' => 'SANCTIONS'],
            ['nom_droit' => 'ALL_FORMATIONS', 'description' => 'Accès complet aux formations', 'module' => 'FORMATIONS'],
            ['nom_droit' => 'READ_FORMATION', 'description' => 'Consulter les formations', 'module' => 'FORMATIONS'],
            ['nom_droit' => 'ALL_DOCUMENTS', 'description' => 'Accès complet aux documents', 'module' => 'DOCUMENTS'],
            ['nom_droit' => 'READ_DOCUMENT', 'description' => 'Consulter les documents', 'module' => 'DOCUMENTS'],
            ['nom_droit' => 'ALL_USERS', 'description' => 'Accès complet aux utilisateurs', 'module' => 'UTILISATEURS'],
            ['nom_droit' => 'READ_USER', 'description' => 'Consulter les utilisateurs', 'module' => 'UTILISATEURS'],
            ['nom_droit' => 'ALL_ROLES', 'description' => 'Accès complet aux rôles', 'module' => 'ROLES'],
            ['nom_droit' => 'READ_ROLE', 'description' => 'Consulter les rôles', 'module' => 'ROLES'],
            ['nom_droit' => 'ALL_DROITS', 'description' => 'Accès complet aux droits', 'module' => 'DROITS'],
            ['nom_droit' => 'READ_DROIT', 'description' => 'Consulter les droits', 'module' => 'DROITS'],
            ['nom_droit' => 'ALL_CONFIGURATIONS', 'description' => 'Accès complet aux configurations', 'module' => 'CONFIGURATIONS'],
            ['nom_droit' => 'READ_CONFIGURATION', 'description' => 'Consulter les configurations', 'module' => 'CONFIGURATIONS'],
        ];

        $created = 0;
        foreach ($defaults as $d) {
            $exists = Droit::where('nom_droit', $d['nom_droit'])->exists();
            if (!$exists) {
                Droit::create(array_merge($d, ['date_creation' => now()]));
                $created++;
            }
        }

        return response()->json(["message" => "$created droit(s) créé(s) avec succès"]);
    }
}
