<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('droits')->orderBy('nom_role')->get();
        return response()->json($roles->map(fn($r) => [
            'id' => $r->id,
            'nomRole' => $r->nom_role,
            'description' => $r->description,
            'dateCreation' => is_string($r->date_creation) ? substr($r->date_creation, 0, 10) : ($r->date_creation?->format('Y-m-d')),
            'droits' => $r->droits,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $nom = $data['nom'] ?? $data['nomRole'] ?? null;

        if (!$nom) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                'nom' => ['Le champ nom est obligatoire.'],
            ]], 422);
        }

        $role = Role::create([
            'nom_role' => $nom,
            'description' => $data['description'] ?? null,
            'date_creation' => now(),
        ]);

        if (!empty($data['droits'])) {
            $role->droits()->attach($data['droits'], ['date_attribution' => now()]);
        }

        $role->load('droits');

        return response()->json([
            'id' => $role->id,
            'nomRole' => $role->nom_role,
            'description' => $role->description,
            'dateCreation' => $role->date_creation?->format('Y-m-d'),
            'droits' => $role->droits,
        ], 201);
    }

    public function show($id)
    {
        $r = Role::with('droits')->findOrFail($id);
        return response()->json([
            'id' => $r->id,
            'nomRole' => $r->nom_role,
            'description' => $r->description,
            'dateCreation' => is_string($r->date_creation) ? substr($r->date_creation, 0, 10) : ($r->date_creation?->format('Y-m-d')),
            'droits' => $r->droits,
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $data = $request->all();

        $role->update([
            'nom_role' => $data['nom'] ?? $data['nomRole'] ?? $role->nom_role,
            'description' => $data['description'] ?? $role->description,
        ]);

        if (isset($data['droits'])) {
            $role->droits()->syncWithPivotValues($data['droits'], ['date_attribution' => now()]);
        }

        $role->load('droits');

        return response()->json([
            'id' => $role->id,
            'nomRole' => $role->nom_role,
            'description' => $role->description,
            'dateCreation' => $role->date_creation?->format('Y-m-d'),
            'droits' => $role->droits,
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->droits()->detach();
        $role->users()->detach();
        $role->delete();
        return response()->json(['message' => 'Rôle supprimé avec succès']);
    }
}
