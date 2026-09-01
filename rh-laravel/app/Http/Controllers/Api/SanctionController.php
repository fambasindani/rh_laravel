<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sanction;
use Illuminate\Http\Request;

class SanctionController extends Controller
{
    public function index(Request $request)
    {
        $query = Sanction::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('type_sanction', 'like', "%{$search}%")
                  ->orWhere('motif', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($s) => [
            'id' => $s->id,
            'idAgent' => $s->agent_id,
            'agentNom' => $s->agent?->nom,
            'agentPrenom' => $s->agent?->prenom,
            'typeSanction' => $s->type_sanction,
            'motif' => $s->motif,
            'dateSanction' => $s->date_sanction?->format('Y-m-d'),
            'reference' => $s->reference,
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
        $sanctions = Sanction::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($sanctions->map(fn($s) => [
            'id' => $s->id,
            'idAgent' => $s->agent_id,
            'agentNom' => $s->agent?->nom,
            'agentPrenom' => $s->agent?->prenom,
            'typeSanction' => $s->type_sanction,
            'motif' => $s->motif,
            'dateSanction' => $s->date_sanction?->format('Y-m-d'),
            'reference' => $s->reference,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $typeSanction = $data['type_sanction'] ?? $data['typeSanction'] ?? null;
        $dateSanction = $data['date_sanction'] ?? $data['dateSanction'] ?? null;

        if (!$agentId || !$typeSanction || !$dateSanction) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$typeSanction ? ['type_sanction' => ['Le champ type_sanction est obligatoire.']] : []),
                ...(!$dateSanction ? ['date_sanction' => ['Le champ date_sanction est obligatoire.']] : []),
            ]], 422);
        }

        $sanction = Sanction::create([
            'agent_id' => $agentId,
            'type_sanction' => $typeSanction,
            'motif' => $data['motif'] ?? null,
            'date_sanction' => $dateSanction,
            'reference' => $data['reference'] ?? $data['numero_decision'] ?? $data['numeroDecision'] ?? null,
        ]);
        $sanction->load('agent');

        return response()->json([
            'id' => $sanction->id,
            'idAgent' => $sanction->agent_id,
            'agentNom' => $sanction->agent?->nom,
            'agentPrenom' => $sanction->agent?->prenom,
            'typeSanction' => $sanction->type_sanction,
            'motif' => $sanction->motif,
            'dateSanction' => $sanction->date_sanction?->format('Y-m-d'),
            'reference' => $sanction->reference,
        ], 201);
    }

    public function show($id)
    {
        $s = Sanction::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $s->id,
            'idAgent' => $s->agent_id,
            'agentNom' => $s->agent?->nom,
            'agentPrenom' => $s->agent?->prenom,
            'typeSanction' => $s->type_sanction,
            'motif' => $s->motif,
            'dateSanction' => $s->date_sanction?->format('Y-m-d'),
            'reference' => $s->reference,
        ]);
    }

    public function update(Request $request, $id)
    {
        $sanction = Sanction::findOrFail($id);
        $data = $request->all();

        $sanction->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $sanction->agent_id,
            'type_sanction' => $data['type_sanction'] ?? $data['typeSanction'] ?? $sanction->type_sanction,
            'motif' => $data['motif'] ?? $sanction->motif,
            'date_sanction' => $data['date_sanction'] ?? $data['dateSanction'] ?? $sanction->date_sanction,
            'reference' => $data['reference'] ?? $data['numero_decision'] ?? $data['numeroDecision'] ?? $sanction->reference,
        ]);
        $sanction->load('agent');

        return response()->json([
            'id' => $sanction->id,
            'idAgent' => $sanction->agent_id,
            'agentNom' => $sanction->agent?->nom,
            'agentPrenom' => $sanction->agent?->prenom,
            'typeSanction' => $sanction->type_sanction,
            'motif' => $sanction->motif,
            'dateSanction' => $sanction->date_sanction?->format('Y-m-d'),
            'reference' => $sanction->reference,
        ]);
    }

    public function destroy($id)
    {
        Sanction::findOrFail($id)->delete();
        return response()->json(['message' => 'Sanction supprimée avec succès']);
    }
}
