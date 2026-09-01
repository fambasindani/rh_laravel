<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Retraite;
use Illuminate\Http\Request;

class RetraiteController extends Controller
{
    public function index(Request $request)
    {
        $query = Retraite::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('observation', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($r) => [
            'id' => $r->id,
            'idAgent' => $r->agent_id,
            'agentNom' => $r->agent?->nom,
            'agentPrenom' => $r->agent?->prenom,
            'dateRetraite' => $r->date_retraite?->format('Y-m-d'),
            'reference' => $r->reference,
            'observation' => $r->observation,
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
        $retraites = Retraite::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($retraites->map(fn($r) => [
            'id' => $r->id,
            'idAgent' => $r->agent_id,
            'agentNom' => $r->agent?->nom,
            'agentPrenom' => $r->agent?->prenom,
            'dateRetraite' => $r->date_retraite?->format('Y-m-d'),
            'reference' => $r->reference,
            'observation' => $r->observation,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $dateRetraite = $data['date_retraite'] ?? $data['dateRetraite'] ?? null;

        if (!$agentId || !$dateRetraite) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$dateRetraite ? ['dateRetraite' => ['Le champ dateRetraite est obligatoire.']] : []),
            ]], 422);
        }

        $retraite = Retraite::create([
            'agent_id' => $agentId,
            'date_retraite' => $dateRetraite,
            'reference' => $data['reference'] ?? null,
            'observation' => $data['observation'] ?? null,
        ]);
        $retraite->load('agent');

        return response()->json([
            'id' => $retraite->id,
            'idAgent' => $retraite->agent_id,
            'agentNom' => $retraite->agent?->nom,
            'agentPrenom' => $retraite->agent?->prenom,
            'dateRetraite' => $retraite->date_retraite?->format('Y-m-d'),
            'reference' => $retraite->reference,
            'observation' => $retraite->observation,
        ], 201);
    }

    public function show($id)
    {
        $r = Retraite::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $r->id,
            'idAgent' => $r->agent_id,
            'agentNom' => $r->agent?->nom,
            'agentPrenom' => $r->agent?->prenom,
            'dateRetraite' => $r->date_retraite?->format('Y-m-d'),
            'reference' => $r->reference,
            'observation' => $r->observation,
        ]);
    }

    public function update(Request $request, $id)
    {
        $retraite = Retraite::findOrFail($id);
        $data = $request->all();

        $retraite->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $retraite->agent_id,
            'date_retraite' => $data['date_retraite'] ?? $data['dateRetraite'] ?? $retraite->date_retraite,
            'reference' => $data['reference'] ?? $retraite->reference,
            'observation' => $data['observation'] ?? $retraite->observation,
        ]);
        $retraite->load('agent');

        return response()->json([
            'id' => $retraite->id,
            'idAgent' => $retraite->agent_id,
            'agentNom' => $retraite->agent?->nom,
            'agentPrenom' => $retraite->agent?->prenom,
            'dateRetraite' => $retraite->date_retraite?->format('Y-m-d'),
            'reference' => $retraite->reference,
            'observation' => $retraite->observation,
        ]);
    }

    public function destroy($id)
    {
        Retraite::findOrFail($id)->delete();
        return response()->json(['message' => 'Retraite supprimée avec succès']);
    }
}
