<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'agent']);

        if ($request->has('keyword') && $request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('username', 'like', "%{$keyword}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        $offset = ($page - 1) * $perPage;

        $total = $query->count();
        $users = $query->orderByDesc('id')->skip($offset)->take($perPage)->get();
        $totalPages = (int) ceil($total / $perPage);

        $formatted = $users->map(fn($u) => [
            'id' => $u->id,
            'username' => $u->username,
            'agentId' => $u->agent_id,
            'actif' => (bool) $u->actif,
            'dateCreation' => is_string($u->date_creation) ? substr($u->date_creation, 0, 10) : ($u->date_creation?->format('Y-m-d')),
            'roles' => $u->roles->map(fn($r) => [
                'id' => $r->id,
                'nomRole' => $r->nom_role,
            ]),
            'agent' => $u->agent ? [
                'id' => $u->agent->id,
                'nom' => $u->agent->nom,
                'prenom' => $u->agent->prenom,
                'matricule' => $u->agent->matricule,
            ] : null,
            'agentNom' => $u->agent?->nom,
            'agentPrenom' => $u->agent?->prenom,
            'agentMatricule' => $u->agent?->matricule,
        ]);

        return response()->json([
            'content' => $formatted,
            'totalElements' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'last' => $page >= $totalPages,
        ]);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $agentId = $input['agentId'] ?? $input['agent_id'] ?? null;

        if (!$agentId) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                'agentId' => ['L\'agent est obligatoire.'],
            ]], 422);
        }

        $agent = Agent::find($agentId);
        if (!$agent) {
            return response()->json(['message' => 'Agent introuvable'], 404);
        }

        $username = $agent->email;

        $validated = $request->validate([
            'password' => 'required|string|min:6',
            'actif' => 'nullable|boolean',
        ]);

        $validated['username'] = $username;
        $validated['agent_id'] = $agentId;
        $validated['password_hash'] = Hash::make($validated['password']);
        unset($validated['password']);
        $validated['actif'] = $validated['actif'] ?? true;
        $validated['date_creation'] = now();

        $roles = $input['roles'] ?? $input['roleIds'] ?? [];

        $user = User::create($validated);

        if (!empty($roles)) {
            $user->roles()->attach($roles, ['date_attribution' => now()]);
        }

        $user->load('roles', 'agent');

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'agentId' => $user->agent_id,
                'actif' => (bool) $user->actif,
                'dateCreation' => is_string($user->date_creation) ? substr($user->date_creation, 0, 10) : ($user->date_creation?->format('Y-m-d')),
                'roles' => $user->roles->map(fn($r) => ['id' => $r->id, 'nomRole' => $r->nom_role]),
                'agentNom' => $user->agent?->nom,
                'agentPrenom' => $user->agent?->prenom,
                'agentMatricule' => $user->agent?->matricule,
            ],
        ], 201);
    }

    public function show($id)
    {
        $user = User::with(['roles', 'agent'])->findOrFail($id);
        return response()->json(['data' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $input = $request->all();

        $validated = $request->validate([
            'password' => 'nullable|string|min:6',
            'actif' => 'nullable|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
            unset($validated['password']);
        } else {
            unset($validated['password']);
        }

        $roles = $input['roles'] ?? $input['roleIds'] ?? null;
        unset($validated['roles'], $validated['roleIds']);

        $user->update($validated);

        if ($roles !== null) {
            $user->roles()->syncWithPivotValues($roles, ['date_attribution' => now()]);
        }

        $user->load('roles');

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }
}
