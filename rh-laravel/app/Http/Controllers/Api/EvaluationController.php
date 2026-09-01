<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $query = Evaluation::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('appreciation', 'like', "%{$search}%")
                  ->orWhere('evaluateur', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($e) => [
            'id' => $e->id,
            'idAgent' => $e->agent_id,
            'agentNom' => $e->agent?->nom,
            'agentPrenom' => $e->agent?->prenom,
            'dateEvaluation' => $e->date_evaluation?->format('Y-m-d'),
            'note' => $e->note,
            'appreciation' => $e->appreciation,
            'evaluateur' => $e->evaluateur,
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
        $evaluations = Evaluation::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($evaluations->map(fn($e) => [
            'id' => $e->id,
            'idAgent' => $e->agent_id,
            'agentNom' => $e->agent?->nom,
            'agentPrenom' => $e->agent?->prenom,
            'dateEvaluation' => $e->date_evaluation?->format('Y-m-d'),
            'note' => $e->note,
            'appreciation' => $e->appreciation,
            'evaluateur' => $e->evaluateur,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $dateEvaluation = $data['date_evaluation'] ?? $data['dateEvaluation'] ?? null;
        $note = $data['note'] ?? null;

        if (!$agentId || !$dateEvaluation || !$note) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$dateEvaluation ? ['dateEvaluation' => ['Le champ dateEvaluation est obligatoire.']] : []),
                ...(!$note ? ['note' => ['Le champ note est obligatoire.']] : []),
            ]], 422);
        }

        $evaluation = Evaluation::create([
            'agent_id' => $agentId,
            'date_evaluation' => $dateEvaluation,
            'note' => $note,
            'appreciation' => $data['appreciation'] ?? null,
            'evaluateur' => $data['evaluateur'] ?? null,
        ]);
        $evaluation->load('agent');

        return response()->json([
            'id' => $evaluation->id,
            'idAgent' => $evaluation->agent_id,
            'agentNom' => $evaluation->agent?->nom,
            'agentPrenom' => $evaluation->agent?->prenom,
            'dateEvaluation' => $evaluation->date_evaluation?->format('Y-m-d'),
            'note' => $evaluation->note,
            'appreciation' => $evaluation->appreciation,
            'evaluateur' => $evaluation->evaluateur,
        ], 201);
    }

    public function show($id)
    {
        $e = Evaluation::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $e->id,
            'idAgent' => $e->agent_id,
            'agentNom' => $e->agent?->nom,
            'agentPrenom' => $e->agent?->prenom,
            'dateEvaluation' => $e->date_evaluation?->format('Y-m-d'),
            'note' => $e->note,
            'appreciation' => $e->appreciation,
            'evaluateur' => $e->evaluateur,
        ]);
    }

    public function update(Request $request, $id)
    {
        $evaluation = Evaluation::findOrFail($id);
        $data = $request->all();

        $evaluation->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $evaluation->agent_id,
            'date_evaluation' => $data['date_evaluation'] ?? $data['dateEvaluation'] ?? $evaluation->date_evaluation,
            'note' => $data['note'] ?? $evaluation->note,
            'appreciation' => $data['appreciation'] ?? $evaluation->appreciation,
            'evaluateur' => $data['evaluateur'] ?? $evaluation->evaluateur,
        ]);
        $evaluation->load('agent');

        return response()->json([
            'id' => $evaluation->id,
            'idAgent' => $evaluation->agent_id,
            'agentNom' => $evaluation->agent?->nom,
            'agentPrenom' => $evaluation->agent?->prenom,
            'dateEvaluation' => $evaluation->date_evaluation?->format('Y-m-d'),
            'note' => $evaluation->note,
            'appreciation' => $evaluation->appreciation,
            'evaluateur' => $evaluation->evaluateur,
        ]);
    }

    public function destroy($id)
    {
        Evaluation::findOrFail($id)->delete();
        return response()->json(['message' => 'Évaluation supprimée avec succès']);
    }
}
