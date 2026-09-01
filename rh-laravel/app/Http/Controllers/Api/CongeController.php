<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conge;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CongeController extends Controller
{
    public function index(Request $request)
    {
        $query = Conge::with(['agent', 'typeConge']);

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->whereHas('agent', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($c) => [
            'id' => $c->id,
            'idAgent' => $c->agent_id,
            'agentNom' => $c->agent?->nom,
            'agentPrenom' => $c->agent?->prenom,
            'idTypeConge' => $c->type_conge_id,
            'typeCongeNom' => $c->typeConge?->nom,
            'dateDebut' => $c->date_debut?->format('Y-m-d'),
            'dateFin' => $c->date_fin?->format('Y-m-d'),
            'nombreJours' => $c->nombre_jours,
            'motif' => $c->motif,
            'statut' => $c->statut,
            'dateDemande' => $c->date_demande?->format('Y-m-d'),
            'dateValidation' => $c->date_validation?->format('Y-m-d'),
            'validateur' => $c->validateur,
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
        $conges = Conge::with(['agent', 'typeConge'])->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($conges->map(fn($c) => [
            'id' => $c->id,
            'idAgent' => $c->agent_id,
            'agentNom' => $c->agent?->nom,
            'agentPrenom' => $c->agent?->prenom,
            'idTypeConge' => $c->type_conge_id,
            'typeCongeNom' => $c->typeConge?->nom,
            'dateDebut' => $c->date_debut?->format('Y-m-d'),
            'dateFin' => $c->date_fin?->format('Y-m-d'),
            'nombreJours' => $c->nombre_jours,
            'motif' => $c->motif,
            'statut' => $c->statut,
            'dateDemande' => $c->date_demande?->format('Y-m-d'),
            'dateValidation' => $c->date_validation?->format('Y-m-d'),
            'validateur' => $c->validateur,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $typeCongeId = $data['type_conge_id'] ?? $data['idTypeConge'] ?? null;
        $dateDebut = $data['date_debut'] ?? $data['dateDebut'] ?? null;
        $dateFin = $data['date_fin'] ?? $data['dateFin'] ?? null;

        if (!$agentId || !$typeCongeId || !$dateDebut || !$dateFin) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$typeCongeId ? ['type_conge_id' => ['Le champ type_conge_id est obligatoire.']] : []),
                ...(!$dateDebut ? ['date_debut' => ['Le champ date_debut est obligatoire.']] : []),
                ...(!$dateFin ? ['date_fin' => ['Le champ date_fin est obligatoire.']] : []),
            ]], 422);
        }

        $nombreJours = Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin)) + 1;

        $conge = Conge::create([
            'agent_id' => $agentId,
            'type_conge_id' => $typeCongeId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nombre_jours' => $data['nombre_jours'] ?? $data['nombreJours'] ?? $nombreJours,
            'motif' => $data['motif'] ?? null,
            'statut' => $data['statut'] ?? 'en_attente',
            'date_demande' => now(),
        ]);
        $conge->load(['agent', 'typeConge']);

        return response()->json([
            'id' => $conge->id,
            'idAgent' => $conge->agent_id,
            'agentNom' => $conge->agent?->nom,
            'agentPrenom' => $conge->agent?->prenom,
            'idTypeConge' => $conge->type_conge_id,
            'typeCongeNom' => $conge->typeConge?->libelle,
            'dateDebut' => $conge->date_debut?->format('Y-m-d'),
            'dateFin' => $conge->date_fin?->format('Y-m-d'),
            'nombreJours' => $conge->nombre_jours,
            'motif' => $conge->motif,
            'statut' => $conge->statut,
        ], 201);
    }

    public function show($id)
    {
        $c = Conge::with(['agent', 'typeConge'])->findOrFail($id);
        return response()->json([
            'id' => $c->id,
            'idAgent' => $c->agent_id,
            'agentNom' => $c->agent?->nom,
            'agentPrenom' => $c->agent?->prenom,
            'idTypeConge' => $c->type_conge_id,
            'typeCongeNom' => $c->typeConge?->nom,
            'dateDebut' => $c->date_debut?->format('Y-m-d'),
            'dateFin' => $c->date_fin?->format('Y-m-d'),
            'nombreJours' => $c->nombre_jours,
            'motif' => $c->motif,
            'statut' => $c->statut,
            'dateValidation' => $c->date_validation?->format('Y-m-d'),
            'validateur' => $c->validateur,
        ]);
    }

    public function update(Request $request, $id)
    {
        $conge = Conge::findOrFail($id);
        $data = $request->all();

        $dateDebut = $data['date_debut'] ?? $data['dateDebut'] ?? $conge->date_debut;
        $dateFin = $data['date_fin'] ?? $data['dateFin'] ?? $conge->date_fin;
        $nombreJours = $dateDebut && $dateFin ? Carbon::parse($dateDebut)->diffInDays(Carbon::parse($dateFin)) + 1 : $conge->nombre_jours;

        $conge->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $conge->agent_id,
            'type_conge_id' => $data['type_conge_id'] ?? $data['idTypeConge'] ?? $conge->type_conge_id,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'nombre_jours' => $data['nombre_jours'] ?? $data['nombreJours'] ?? $nombreJours,
            'motif' => $data['motif'] ?? $conge->motif,
            'statut' => $data['statut'] ?? $conge->statut,
            'date_validation' => $data['date_validation'] ?? $data['dateValidation'] ?? $conge->date_validation,
            'validateur' => $data['validateur'] ?? $conge->validateur,
        ]);
        $conge->load(['agent', 'typeConge']);

        return response()->json([
            'id' => $conge->id,
            'idAgent' => $conge->agent_id,
            'agentNom' => $conge->agent?->nom,
            'agentPrenom' => $conge->agent?->prenom,
            'idTypeConge' => $conge->type_conge_id,
            'typeCongeNom' => $conge->typeConge?->libelle,
            'dateDebut' => $conge->date_debut?->format('Y-m-d'),
            'dateFin' => $conge->date_fin?->format('Y-m-d'),
            'nombreJours' => $conge->nombre_jours,
            'motif' => $conge->motif,
            'statut' => $conge->statut,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $conge = Conge::findOrFail($id);
        $validated = $request->validate([
            'statut' => 'required|string|in:en_attente,approuve,rejete',
        ]);
        $conge->update($validated);
        $conge->load(['agent', 'typeConge']);

        return response()->json([
            'id' => $conge->id,
            'idAgent' => $conge->agent_id,
            'agentNom' => $conge->agent?->nom,
            'agentPrenom' => $conge->agent?->prenom,
            'statut' => $conge->statut,
        ]);
    }

    public function destroy($id)
    {
        Conge::findOrFail($id)->delete();
        return response()->json(['message' => 'Congé supprimé avec succès']);
    }
}
