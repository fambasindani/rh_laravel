<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('motif', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($p) => [
            'id' => $p->id,
            'idAgent' => $p->agent_id,
            'agentNom' => $p->agent?->nom,
            'agentPrenom' => $p->agent?->prenom,
            'datePermission' => $p->date_permission?->format('Y-m-d'),
            'heureSortie' => $p->heure_sortie,
            'heureRetour' => $p->heure_retour,
            'motif' => $p->motif,
            'statut' => $p->statut,
        ]);

        return response()->json([
            'data' => $formatted,
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    public function all()
    {
        $permissions = Permission::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $datePermission = $data['date_permission'] ?? $data['datePermission'] ?? null;
        $heureSortie = $data['heure_sortie'] ?? $data['heureSortie'] ?? null;
        $heureRetour = $data['heure_retour'] ?? $data['heureRetour'] ?? null;

        if (!$agentId) {
            return response()->json(['message' => 'Validation échouée', 'errors' => ['agent_id' => ['Le champ agent_id est obligatoire.']]], 422);
        }
        if (!$datePermission) {
            return response()->json(['message' => 'Validation échouée', 'errors' => ['date_permission' => ['Le champ date_permission est obligatoire.']]], 422);
        }

        $permission = Permission::create([
            'agent_id' => $agentId,
            'date_permission' => $datePermission,
            'heure_sortie' => $heureSortie,
            'heure_retour' => $heureRetour,
            'motif' => $data['motif'] ?? null,
            'statut' => $data['statut'] ?? 'EN_ATTENTE',
        ]);
        $permission->load('agent');

        return response()->json([
            'id' => $permission->id,
            'idAgent' => $permission->agent_id,
            'agentNom' => $permission->agent?->nom,
            'agentPrenom' => $permission->agent?->prenom,
            'datePermission' => $permission->date_permission?->format('Y-m-d'),
            'heureSortie' => $permission->heure_sortie,
            'heureRetour' => $permission->heure_retour,
            'motif' => $permission->motif,
            'statut' => $permission->statut,
        ], 201);
    }

    public function show($id)
    {
        $permission = Permission::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $permission->id,
            'idAgent' => $permission->agent_id,
            'agentNom' => $permission->agent?->nom,
            'agentPrenom' => $permission->agent?->prenom,
            'datePermission' => $permission->date_permission?->format('Y-m-d'),
            'heureSortie' => $permission->heure_sortie,
            'heureRetour' => $permission->heure_retour,
            'motif' => $permission->motif,
            'statut' => $permission->statut,
        ]);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $data = $request->all();

        $permission->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $permission->agent_id,
            'date_permission' => $data['date_permission'] ?? $data['datePermission'] ?? $permission->date_permission,
            'heure_sortie' => $data['heure_sortie'] ?? $data['heureSortie'] ?? $permission->heure_sortie,
            'heure_retour' => $data['heure_retour'] ?? $data['heureRetour'] ?? $permission->heure_retour,
            'motif' => $data['motif'] ?? $permission->motif,
            'statut' => $data['statut'] ?? $permission->statut,
        ]);
        $permission->load('agent');

        return response()->json([
            'id' => $permission->id,
            'idAgent' => $permission->agent_id,
            'agentNom' => $permission->agent?->nom,
            'agentPrenom' => $permission->agent?->prenom,
            'datePermission' => $permission->date_permission?->format('Y-m-d'),
            'heureSortie' => $permission->heure_sortie,
            'heureRetour' => $permission->heure_retour,
            'motif' => $permission->motif,
            'statut' => $permission->statut,
        ]);
    }

    public function destroy($id)
    {
        Permission::findOrFail($id)->delete();
        return response()->json(['message' => 'Permission supprimée avec succès']);
    }
}
