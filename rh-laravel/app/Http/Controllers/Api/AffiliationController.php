<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Affiliation;
use Illuminate\Http\Request;

class AffiliationController extends Controller
{
    public function index(Request $request)
    {
        $query = Affiliation::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('postnom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('agent_id') && $request->agent_id) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->has('statut') && $request->statut !== null && $request->statut !== '') {
            $query->where('statut', filter_var($request->statut, FILTER_VALIDATE_BOOLEAN));
        }

        $affiliations = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $affiliations->getCollection()->map(fn($a) => [
            'id' => $a->id,
            'idAgent' => $a->agent_id,
            'agentNom' => $a->agent?->nom,
            'agentPostnom' => $a->agent?->postnom,
            'agentPrenom' => $a->agent?->prenom,
            'nom' => $a->nom,
            'postnom' => $a->postnom,
            'prenom' => $a->prenom,
            'dateNaissance' => $a->date_naissance,
            'lieuNaissance' => $a->lieu_naissance,
            'relation' => $a->relation,
            'etat' => $a->etat,
            'statut' => $a->statut,
        ]);

        return response()->json([
            'data' => $formatted,
            'current_page' => $affiliations->currentPage(),
            'per_page' => $affiliations->perPage(),
            'total' => $affiliations->total(),
            'last_page' => $affiliations->lastPage(),
        ]);
    }

    public function all()
    {
        $affiliations = Affiliation::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($affiliations->map(fn($a) => [
            'id' => $a->id,
            'idAgent' => $a->agent_id,
            'agentNom' => $a->agent?->nom,
            'agentPostnom' => $a->agent?->postnom,
            'agentPrenom' => $a->agent?->prenom,
            'nom' => $a->nom,
            'postnom' => $a->postnom,
            'prenom' => $a->prenom,
            'dateNaissance' => $a->date_naissance,
            'lieuNaissance' => $a->lieu_naissance,
            'relation' => $a->relation,
            'etat' => $a->etat,
            'statut' => $a->statut,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;

        $nom = $data['nom'] ?? null;
        if (!$agentId || !$nom) {
            return response()->json([
                'message' => 'Validation échouée',
                'errors' => [
                    ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                    ...(!$nom ? ['nom' => ['Le champ nom est obligatoire.']] : []),
                ]
            ], 422);
        }

        $validated = [
            'agent_id' => $agentId,
            'nom' => $nom,
            'postnom' => $data['postnom'] ?? null,
            'prenom' => $data['prenom'] ?? null,
            'date_naissance' => $data['date_naissance'] ?? $data['dateNaissance'] ?? null,
            'lieu_naissance' => $data['lieu_naissance'] ?? $data['lieuNaissance'] ?? null,
            'relation' => $data['relation'] ?? null,
            'etat' => $data['etat'] ?? null,
            'statut' => isset($data['statut']) ? filter_var($data['statut'], FILTER_VALIDATE_BOOLEAN) : true,
        ];

        $affiliation = Affiliation::create($validated);
        $affiliation->load('agent');

        return response()->json([
            'id' => $affiliation->id,
            'idAgent' => $affiliation->agent_id,
            'agentNom' => $affiliation->agent->nom ?? '',
            'agentPostnom' => $affiliation->agent->postnom ?? '',
            'agentPrenom' => $affiliation->agent->prenom ?? '',
            'nom' => $affiliation->nom,
            'postnom' => $affiliation->postnom,
            'prenom' => $affiliation->prenom,
            'dateNaissance' => $affiliation->date_naissance,
            'lieuNaissance' => $affiliation->lieu_naissance,
            'relation' => $affiliation->relation,
            'etat' => $affiliation->etat,
            'statut' => $affiliation->statut,
        ], 201);
    }

    public function show($id)
    {
        $affiliation = Affiliation::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $affiliation->id,
            'idAgent' => $affiliation->agent_id,
            'agentNom' => $affiliation->agent->nom ?? '',
            'agentPostnom' => $affiliation->agent->postnom ?? '',
            'agentPrenom' => $affiliation->agent->prenom ?? '',
            'nom' => $affiliation->nom,
            'postnom' => $affiliation->postnom,
            'prenom' => $affiliation->prenom,
            'dateNaissance' => $affiliation->date_naissance,
            'lieuNaissance' => $affiliation->lieu_naissance,
            'relation' => $affiliation->relation,
            'etat' => $affiliation->etat,
            'statut' => $affiliation->statut,
        ]);
    }

    public function update(Request $request, $id)
    {
        $affiliation = Affiliation::findOrFail($id);
        $data = $request->all();

        $validated = [
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $affiliation->agent_id,
            'nom' => $data['nom'] ?? $affiliation->nom,
            'postnom' => $data['postnom'] ?? $affiliation->postnom,
            'prenom' => $data['prenom'] ?? $affiliation->prenom,
            'date_naissance' => $data['date_naissance'] ?? $data['dateNaissance'] ?? $affiliation->date_naissance,
            'lieu_naissance' => $data['lieu_naissance'] ?? $data['lieuNaissance'] ?? $affiliation->lieu_naissance,
            'relation' => $data['relation'] ?? $affiliation->relation,
            'etat' => $data['etat'] ?? $affiliation->etat,
            'statut' => isset($data['statut']) ? filter_var($data['statut'], FILTER_VALIDATE_BOOLEAN) : $affiliation->statut,
        ];

        $affiliation->update($validated);
        $affiliation->load('agent');

        return response()->json([
            'id' => $affiliation->id,
            'idAgent' => $affiliation->agent_id,
            'agentNom' => $affiliation->agent->nom ?? '',
            'agentPostnom' => $affiliation->agent->postnom ?? '',
            'agentPrenom' => $affiliation->agent->prenom ?? '',
            'nom' => $affiliation->nom,
            'postnom' => $affiliation->postnom,
            'prenom' => $affiliation->prenom,
            'dateNaissance' => $affiliation->date_naissance,
            'lieuNaissance' => $affiliation->lieu_naissance,
            'relation' => $affiliation->relation,
            'etat' => $affiliation->etat,
            'statut' => $affiliation->statut,
        ]);
    }

    public function destroy($id)
    {
        Affiliation::findOrFail($id)->delete();
        return response()->json(['message' => 'Dépendant supprimé avec succès']);
    }
}
