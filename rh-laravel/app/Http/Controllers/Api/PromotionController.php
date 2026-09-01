<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $query = Promotion::with(['agent', 'grade']);

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $promotions = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        return response()->json($promotions);
    }

    public function all()
    {
        $promotions = Promotion::with(['agent', 'grade'])->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($promotions);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $gradeId = $data['grade_id'] ?? $data['idGrade'] ?? null;
        $dateDebut = $data['date_debut'] ?? $data['dateDebut'] ?? null;

        if (!$agentId) {
            return response()->json(['message' => 'Validation échouée', 'errors' => ['agent_id' => ['Le champ agent_id est obligatoire.']]], 422);
        }
        if (!$gradeId) {
            return response()->json(['message' => 'Validation échouée', 'errors' => ['grade_id' => ['Le champ grade_id est obligatoire.']]], 422);
        }
        if (!$dateDebut) {
            return response()->json(['message' => 'Validation échouée', 'errors' => ['date_debut' => ['Le champ date_debut est obligatoire.']]], 422);
        }

        $promotion = Promotion::create([
            'agent_id' => $agentId,
            'grade_id' => $gradeId,
            'date_debut' => $dateDebut,
            'date_fin' => $data['date_fin'] ?? $data['dateFin'] ?? null,
            'reference' => $data['reference'] ?? null,
        ]);
        $promotion->load(['agent', 'grade']);

        return response()->json($promotion, 201);
    }

    public function show($id)
    {
        $promotion = Promotion::with(['agent', 'grade'])->findOrFail($id);
        return response()->json($promotion);
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);
        $data = $request->all();

        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? $promotion->agent_id;
        $gradeId = $data['grade_id'] ?? $data['idGrade'] ?? $promotion->grade_id;
        $dateDebut = $data['date_debut'] ?? $data['dateDebut'] ?? $promotion->date_debut;

        $promotion->update([
            'agent_id' => $agentId,
            'grade_id' => $gradeId,
            'date_debut' => $dateDebut,
            'date_fin' => $data['date_fin'] ?? $data['dateFin'] ?? $promotion->date_fin,
            'reference' => $data['reference'] ?? $promotion->reference,
        ]);
        $promotion->load(['agent', 'grade']);

        return response()->json($promotion);
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return response()->json(['message' => 'Promotion supprimée avec succès']);
    }
}
