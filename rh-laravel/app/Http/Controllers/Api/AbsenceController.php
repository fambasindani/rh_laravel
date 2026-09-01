<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Absence::with('agent');

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
            'dateDebut' => $a->date_debut?->format('Y-m-d'),
            'dateFin' => $a->date_fin?->format('Y-m-d'),
            'motif' => $a->motif,
            'statut' => $a->statut,
            'justification' => $a->justification,
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
        $absences = Absence::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($absences->map(fn($a) => [
            'id' => $a->id,
            'idAgent' => $a->agent_id,
            'agentNom' => $a->agent?->nom,
            'agentPrenom' => $a->agent?->prenom,
            'dateDebut' => $a->date_debut?->format('Y-m-d'),
            'dateFin' => $a->date_fin?->format('Y-m-d'),
            'motif' => $a->motif,
            'statut' => $a->statut,
            'justification' => $a->justification,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $dateDebut = $data['date_debut'] ?? $data['dateDebut'] ?? null;
        $dateFin = $data['date_fin'] ?? $data['dateFin'] ?? null;

        if (!$agentId || !$dateDebut || !$dateFin) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$dateDebut ? ['date_debut' => ['Le champ date_debut est obligatoire.']] : []),
                ...(!$dateFin ? ['date_fin' => ['Le champ date_fin est obligatoire.']] : []),
            ]], 422);
        }

        $absence = Absence::create([
            'agent_id' => $agentId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'motif' => $data['motif'] ?? null,
            'justification' => $data['justification'] ?? null,
            'statut' => $data['statut'] ?? null,
        ]);
        $absence->load('agent');

        return response()->json([
            'id' => $absence->id,
            'idAgent' => $absence->agent_id,
            'agentNom' => $absence->agent?->nom,
            'agentPrenom' => $absence->agent?->prenom,
            'dateDebut' => $absence->date_debut?->format('Y-m-d'),
            'dateFin' => $absence->date_fin?->format('Y-m-d'),
            'motif' => $absence->motif,
            'statut' => $absence->statut,
            'justifiee' => $absence->justifiee,
        ], 201);
    }

    public function show($id)
    {
        $a = Absence::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $a->id,
            'idAgent' => $a->agent_id,
            'agentNom' => $a->agent?->nom,
            'agentPrenom' => $a->agent?->prenom,
            'dateDebut' => $a->date_debut?->format('Y-m-d'),
            'dateFin' => $a->date_fin?->format('Y-m-d'),
            'motif' => $a->motif,
            'statut' => $a->statut,
            'justification' => $a->justification,
        ]);
    }

    public function update(Request $request, $id)
    {
        $absence = Absence::findOrFail($id);
        $data = $request->all();

        $absence->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $absence->agent_id,
            'date_debut' => $data['date_debut'] ?? $data['dateDebut'] ?? $absence->date_debut,
            'date_fin' => $data['date_fin'] ?? $data['dateFin'] ?? $absence->date_fin,
            'motif' => $data['motif'] ?? $absence->motif,
            'justification' => $data['justification'] ?? $absence->justification,
            'statut' => $data['statut'] ?? $absence->statut,
        ]);
        $absence->load('agent');

        return response()->json([
            'id' => $absence->id,
            'idAgent' => $absence->agent_id,
            'agentNom' => $absence->agent?->nom,
            'agentPrenom' => $absence->agent?->prenom,
            'dateDebut' => $absence->date_debut?->format('Y-m-d'),
            'dateFin' => $absence->date_fin?->format('Y-m-d'),
            'motif' => $absence->motif,
            'statut' => $absence->statut,
            'justifiee' => $absence->justifiee,
        ]);
    }

    public function destroy($id)
    {
        Absence::findOrFail($id)->delete();
        return response()->json(['message' => 'Absence supprimée avec succès']);
    }
}
