<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    public function index(Request $request)
    {
        $query = Affectation::with(['agent', 'direction']);

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->whereHas('agent', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($a) => [
            'id' => $a->id,
            'idAgent' => $a->agent_id,
            'agentNom' => $a->agent?->nom,
            'agentPrenom' => $a->agent?->prenom,
            'idDirection' => $a->direction_id,
            'directionNom' => $a->direction?->nom,
            'dateDebut' => $a->date_debut?->format('Y-m-d'),
            'dateFin' => $a->date_fin?->format('Y-m-d'),
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
        $affectations = Affectation::with(['agent', 'direction'])->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($affectations->map(fn($a) => [
            'id' => $a->id,
            'idAgent' => $a->agent_id,
            'agentNom' => $a->agent?->nom,
            'agentPrenom' => $a->agent?->prenom,
            'idDirection' => $a->direction_id,
            'directionNom' => $a->direction?->nom,
            'dateDebut' => $a->date_debut?->format('Y-m-d'),
            'dateFin' => $a->date_fin?->format('Y-m-d'),
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $directionId = $data['direction_id'] ?? $data['idDirection'] ?? null;
        $dateDebut = $data['date_debut'] ?? $data['dateDebut'] ?? null;

        if (!$agentId || !$directionId || !$dateDebut) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$directionId ? ['direction_id' => ['Le champ direction_id est obligatoire.']] : []),
                ...(!$dateDebut ? ['date_debut' => ['Le champ date_debut est obligatoire.']] : []),
            ]], 422);
        }

        $affectation = Affectation::create([
            'agent_id' => $agentId,
            'direction_id' => $directionId,
            'date_debut' => $dateDebut,
            'date_fin' => $data['date_fin'] ?? $data['dateFin'] ?? null,
        ]);
        $affectation->load(['agent', 'direction']);

        return response()->json([
            'id' => $affectation->id,
            'idAgent' => $affectation->agent_id,
            'agentNom' => $affectation->agent?->nom,
            'agentPrenom' => $affectation->agent?->prenom,
            'idDirection' => $affectation->direction_id,
            'directionNom' => $affectation->direction?->nom,
            'dateDebut' => $affectation->date_debut?->format('Y-m-d'),
            'dateFin' => $affectation->date_fin?->format('Y-m-d'),
        ], 201);
    }

    public function show($id)
    {
        $a = Affectation::with(['agent', 'direction'])->findOrFail($id);
        return response()->json([
            'id' => $a->id,
            'idAgent' => $a->agent_id,
            'agentNom' => $a->agent?->nom,
            'agentPrenom' => $a->agent?->prenom,
            'idDirection' => $a->direction_id,
            'directionNom' => $a->direction?->nom,
            'dateDebut' => $a->date_debut?->format('Y-m-d'),
            'dateFin' => $a->date_fin?->format('Y-m-d'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $affectation = Affectation::findOrFail($id);
        $data = $request->all();

        $affectation->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $affectation->agent_id,
            'direction_id' => $data['direction_id'] ?? $data['idDirection'] ?? $affectation->direction_id,
            'date_debut' => $data['date_debut'] ?? $data['dateDebut'] ?? $affectation->date_debut,
            'date_fin' => $data['date_fin'] ?? $data['dateFin'] ?? $affectation->date_fin,
        ]);
        $affectation->load(['agent', 'direction']);

        return response()->json([
            'id' => $affectation->id,
            'idAgent' => $affectation->agent_id,
            'agentNom' => $affectation->agent?->nom,
            'agentPrenom' => $affectation->agent?->prenom,
            'idDirection' => $affectation->direction_id,
            'directionNom' => $affectation->direction?->nom,
            'dateDebut' => $affectation->date_debut?->format('Y-m-d'),
            'dateFin' => $affectation->date_fin?->format('Y-m-d'),
        ]);
    }

    public function destroy($id)
    {
        Affectation::findOrFail($id)->delete();
        return response()->json(['message' => 'Affectation supprimée avec succès']);
    }
}
