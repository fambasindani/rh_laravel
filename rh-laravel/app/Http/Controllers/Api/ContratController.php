<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use Illuminate\Http\Request;

class ContratController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrat::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('type_contrat', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($c) => [
            'id' => $c->id,
            'idAgent' => $c->agent_id,
            'agentNom' => $c->agent?->nom,
            'agentPrenom' => $c->agent?->prenom,
            'typeContrat' => $c->type_contrat,
            'reference' => $c->reference,
            'dateDebut' => $c->date_debut?->format('Y-m-d'),
            'dateFin' => $c->date_fin?->format('Y-m-d'),
            'statut' => $c->statut,
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
        $contrats = Contrat::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($contrats->map(fn($c) => [
            'id' => $c->id,
            'idAgent' => $c->agent_id,
            'agentNom' => $c->agent?->nom,
            'agentPrenom' => $c->agent?->prenom,
            'typeContrat' => $c->type_contrat,
            'reference' => $c->reference,
            'dateDebut' => $c->date_debut?->format('Y-m-d'),
            'dateFin' => $c->date_fin?->format('Y-m-d'),
            'statut' => $c->statut,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $typeContrat = $data['type_contrat'] ?? $data['typeContrat'] ?? null;
        $dateDebut = $data['date_debut'] ?? $data['dateDebut'] ?? null;

        if (!$agentId || !$typeContrat || !$dateDebut) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$typeContrat ? ['type_contrat' => ['Le champ type_contrat est obligatoire.']] : []),
                ...(!$dateDebut ? ['date_debut' => ['Le champ date_debut est obligatoire.']] : []),
            ]], 422);
        }

        $contrat = Contrat::create([
            'agent_id' => $agentId,
            'type_contrat' => $typeContrat,
            'reference' => $data['reference'] ?? null,
            'date_debut' => $dateDebut,
            'date_fin' => $data['date_fin'] ?? $data['dateFin'] ?? null,
            'statut' => $data['statut'] ?? null,
        ]);
        $contrat->load('agent');

        return response()->json([
            'id' => $contrat->id,
            'idAgent' => $contrat->agent_id,
            'agentNom' => $contrat->agent?->nom,
            'agentPrenom' => $contrat->agent?->prenom,
            'typeContrat' => $contrat->type_contrat,
            'reference' => $contrat->reference,
            'dateDebut' => $contrat->date_debut?->format('Y-m-d'),
            'dateFin' => $contrat->date_fin?->format('Y-m-d'),
            'statut' => $contrat->statut,
        ], 201);
    }

    public function show($id)
    {
        $c = Contrat::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $c->id,
            'idAgent' => $c->agent_id,
            'agentNom' => $c->agent?->nom,
            'agentPrenom' => $c->agent?->prenom,
            'typeContrat' => $c->type_contrat,
            'reference' => $c->reference,
            'dateDebut' => $c->date_debut?->format('Y-m-d'),
            'dateFin' => $c->date_fin?->format('Y-m-d'),
            'statut' => $c->statut,
        ]);
    }

    public function update(Request $request, $id)
    {
        $contrat = Contrat::findOrFail($id);
        $data = $request->all();

        $contrat->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $contrat->agent_id,
            'type_contrat' => $data['type_contrat'] ?? $data['typeContrat'] ?? $contrat->type_contrat,
            'reference' => $data['reference'] ?? $contrat->reference,
            'date_debut' => $data['date_debut'] ?? $data['dateDebut'] ?? $contrat->date_debut,
            'date_fin' => $data['date_fin'] ?? $data['dateFin'] ?? $contrat->date_fin,
            'statut' => $data['statut'] ?? $contrat->statut,
        ]);
        $contrat->load('agent');

        return response()->json([
            'id' => $contrat->id,
            'idAgent' => $contrat->agent_id,
            'agentNom' => $contrat->agent?->nom,
            'agentPrenom' => $contrat->agent?->prenom,
            'typeContrat' => $contrat->type_contrat,
            'reference' => $contrat->reference,
            'dateDebut' => $contrat->date_debut?->format('Y-m-d'),
            'dateFin' => $contrat->date_fin?->format('Y-m-d'),
            'statut' => $contrat->statut,
        ]);
    }

    public function destroy($id)
    {
        Contrat::findOrFail($id)->delete();
        return response()->json(['message' => 'Contrat supprimé avec succès']);
    }
}
