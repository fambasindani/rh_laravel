<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Mission::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('lieu', 'like', "%{$search}%")
                  ->orWhere('motif', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($m) => [
            'id' => $m->id,
            'idAgent' => $m->agent_id,
            'agentNom' => $m->agent?->nom,
            'agentPrenom' => $m->agent?->prenom,
            'lieu' => $m->lieu,
            'motif' => $m->motif,
            'dateDepart' => $m->date_depart?->format('Y-m-d'),
            'dateRetour' => $m->date_retour?->format('Y-m-d'),
            'reference' => $m->reference,
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
        $missions = Mission::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($missions->map(fn($m) => [
            'id' => $m->id,
            'idAgent' => $m->agent_id,
            'agentNom' => $m->agent?->nom,
            'agentPrenom' => $m->agent?->prenom,
            'lieu' => $m->lieu,
            'motif' => $m->motif,
            'dateDepart' => $m->date_depart?->format('Y-m-d'),
            'dateRetour' => $m->date_retour?->format('Y-m-d'),
            'reference' => $m->reference,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $lieu = $data['lieu'] ?? null;
        $motif = $data['motif'] ?? null;
        $dateDepart = $data['date_depart'] ?? $data['dateDepart'] ?? null;

        if (!$agentId || !$lieu || !$motif || !$dateDepart) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$lieu ? ['lieu' => ['Le champ lieu est obligatoire.']] : []),
                ...(!$motif ? ['motif' => ['Le champ motif est obligatoire.']] : []),
                ...(!$dateDepart ? ['dateDepart' => ['Le champ dateDepart est obligatoire.']] : []),
            ]], 422);
        }

        $mission = Mission::create([
            'agent_id' => $agentId,
            'lieu' => $lieu,
            'motif' => $motif,
            'date_depart' => $dateDepart,
            'date_retour' => $data['date_retour'] ?? $data['dateRetour'] ?? null,
            'reference' => $data['reference'] ?? null,
        ]);
        $mission->load('agent');

        return response()->json([
            'id' => $mission->id,
            'idAgent' => $mission->agent_id,
            'agentNom' => $mission->agent?->nom,
            'agentPrenom' => $mission->agent?->prenom,
            'lieu' => $mission->lieu,
            'motif' => $mission->motif,
            'dateDepart' => $mission->date_depart?->format('Y-m-d'),
            'dateRetour' => $mission->date_retour?->format('Y-m-d'),
            'reference' => $mission->reference,
        ], 201);
    }

    public function show($id)
    {
        $m = Mission::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $m->id,
            'idAgent' => $m->agent_id,
            'agentNom' => $m->agent?->nom,
            'agentPrenom' => $m->agent?->prenom,
            'lieu' => $m->lieu,
            'motif' => $m->motif,
            'dateDepart' => $m->date_depart?->format('Y-m-d'),
            'dateRetour' => $m->date_retour?->format('Y-m-d'),
            'reference' => $m->reference,
        ]);
    }

    public function update(Request $request, $id)
    {
        $mission = Mission::findOrFail($id);
        $data = $request->all();

        $mission->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $mission->agent_id,
            'lieu' => $data['lieu'] ?? $mission->lieu,
            'motif' => $data['motif'] ?? $mission->motif,
            'date_depart' => $data['date_depart'] ?? $data['dateDepart'] ?? $mission->date_depart,
            'date_retour' => $data['date_retour'] ?? $data['dateRetour'] ?? $mission->date_retour,
            'reference' => $data['reference'] ?? $mission->reference,
        ]);
        $mission->load('agent');

        return response()->json([
            'id' => $mission->id,
            'idAgent' => $mission->agent_id,
            'agentNom' => $mission->agent?->nom,
            'agentPrenom' => $mission->agent?->prenom,
            'lieu' => $mission->lieu,
            'motif' => $mission->motif,
            'dateDepart' => $mission->date_depart?->format('Y-m-d'),
            'dateRetour' => $mission->date_retour?->format('Y-m-d'),
            'reference' => $mission->reference,
        ]);
    }

    public function destroy($id)
    {
        Mission::findOrFail($id)->delete();
        return response()->json(['message' => 'Mission supprimée avec succès']);
    }
}
