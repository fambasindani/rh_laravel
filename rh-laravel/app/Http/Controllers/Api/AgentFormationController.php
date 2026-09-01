<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgentFormation;
use Illuminate\Http\Request;

class AgentFormationController extends Controller
{
    public function index(Request $request)
    {
        $query = AgentFormation::with(['agent', 'formation']);

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('observation', 'like', "%{$search}%")
                  ->orWhere('resultat', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  })
                  ->orWhereHas('formation', function ($fq) use ($search) {
                      $fq->where('intitule', 'like', "%{$search}%");
                  });
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($af) => [
            'id' => $af->id,
            'idAgent' => $af->agent_id,
            'agentNom' => $af->agent?->nom,
            'agentPrenom' => $af->agent?->prenom,
            'idFormation' => $af->formation_id,
            'formationIntitule' => $af->formation?->intitule,
            'resultat' => $af->resultat,
            'observation' => $af->observation,
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
        $agentFormations = AgentFormation::with(['agent', 'formation'])->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($agentFormations->map(fn($af) => [
            'id' => $af->id,
            'idAgent' => $af->agent_id,
            'agentNom' => $af->agent?->nom,
            'agentPrenom' => $af->agent?->prenom,
            'idFormation' => $af->formation_id,
            'formationIntitule' => $af->formation?->intitule,
            'resultat' => $af->resultat,
            'observation' => $af->observation,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $formationId = $data['formation_id'] ?? $data['idFormation'] ?? null;

        if (!$agentId || !$formationId) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$formationId ? ['formation_id' => ['Le champ formation_id est obligatoire.']] : []),
            ]], 422);
        }

        $af = AgentFormation::create([
            'agent_id' => $agentId,
            'formation_id' => $formationId,
            'resultat' => $data['resultat'] ?? $data['statut'] ?? null,
            'observation' => $data['observation'] ?? null,
        ]);
        $af->load(['agent', 'formation']);

        return response()->json([
            'id' => $af->id,
            'idAgent' => $af->agent_id,
            'agentNom' => $af->agent?->nom,
            'agentPrenom' => $af->agent?->prenom,
            'idFormation' => $af->formation_id,
            'formationIntitule' => $af->formation?->intitule,
            'resultat' => $af->resultat,
            'observation' => $af->observation,
        ], 201);
    }

    public function show($id)
    {
        $af = AgentFormation::with(['agent', 'formation'])->findOrFail($id);
        return response()->json([
            'id' => $af->id,
            'idAgent' => $af->agent_id,
            'agentNom' => $af->agent?->nom,
            'agentPrenom' => $af->agent?->prenom,
            'idFormation' => $af->formation_id,
            'formationIntitule' => $af->formation?->intitule,
            'resultat' => $af->resultat,
            'observation' => $af->observation,
        ]);
    }

    public function update(Request $request, $id)
    {
        $af = AgentFormation::findOrFail($id);
        $data = $request->all();

        $af->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $af->agent_id,
            'formation_id' => $data['formation_id'] ?? $data['idFormation'] ?? $af->formation_id,
            'resultat' => $data['resultat'] ?? $data['statut'] ?? $af->resultat,
            'observation' => $data['observation'] ?? $af->observation,
        ]);
        $af->load(['agent', 'formation']);

        return response()->json([
            'id' => $af->id,
            'idAgent' => $af->agent_id,
            'agentNom' => $af->agent?->nom,
            'agentPrenom' => $af->agent?->prenom,
            'idFormation' => $af->formation_id,
            'formationIntitule' => $af->formation?->intitule,
            'resultat' => $af->resultat,
            'observation' => $af->observation,
        ]);
    }

    public function destroy($id)
    {
        AgentFormation::findOrFail($id)->delete();
        return response()->json(['message' => 'Participation supprimée avec succès']);
    }
}
