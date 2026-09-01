<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Presence::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->whereHas('agent', function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($p) => [
            'id' => $p->id,
            'idAgent' => $p->agent_id,
            'agentNom' => $p->agent?->nom,
            'agentPrenom' => $p->agent?->prenom,
            'datePresence' => $p->date_presence?->format('Y-m-d'),
            'heureArrivee' => $p->heure_arrivee,
            'heureDepart' => $p->heure_depart,
            'statut' => $p->statut,
            'observation' => $p->observation,
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
        $presences = Presence::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($presences->map(fn($p) => [
            'id' => $p->id,
            'idAgent' => $p->agent_id,
            'agentNom' => $p->agent?->nom,
            'agentPrenom' => $p->agent?->prenom,
            'datePresence' => $p->date_presence?->format('Y-m-d'),
            'heureArrivee' => $p->heure_arrivee,
            'heureDepart' => $p->heure_depart,
            'statut' => $p->statut,
            'observation' => $p->observation,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $datePresence = $data['date_presence'] ?? $data['datePresence'] ?? null;

        if (!$agentId || !$datePresence) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$datePresence ? ['date_presence' => ['Le champ date_presence est obligatoire.']] : []),
            ]], 422);
        }

        $presence = Presence::create([
            'agent_id' => $agentId,
            'date_presence' => $datePresence,
            'heure_arrivee' => $data['heure_arrivee'] ?? $data['heureArrivee'] ?? null,
            'heure_depart' => $data['heure_depart'] ?? $data['heureDepart'] ?? null,
            'statut' => $data['statut'] ?? null,
            'observation' => $data['observation'] ?? null,
        ]);
        $presence->load('agent');

        return response()->json([
            'id' => $presence->id,
            'idAgent' => $presence->agent_id,
            'agentNom' => $presence->agent?->nom,
            'agentPrenom' => $presence->agent?->prenom,
            'datePresence' => $presence->date_presence?->format('Y-m-d'),
            'heureArrivee' => $presence->heure_arrivee,
            'heureDepart' => $presence->heure_depart,
            'statut' => $presence->statut,
            'retardMinutes' => $presence->retard_minutes,
            'observation' => $presence->observation,
        ], 201);
    }

    public function show($id)
    {
        $p = Presence::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $p->id,
            'idAgent' => $p->agent_id,
            'agentNom' => $p->agent?->nom,
            'agentPrenom' => $p->agent?->prenom,
            'datePresence' => $p->date_presence?->format('Y-m-d'),
            'heureArrivee' => $p->heure_arrivee,
            'heureDepart' => $p->heure_depart,
            'statut' => $p->statut,
            'observation' => $p->observation,
        ]);
    }

    public function update(Request $request, $id)
    {
        $presence = Presence::findOrFail($id);
        $data = $request->all();

        $presence->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $presence->agent_id,
            'date_presence' => $data['date_presence'] ?? $data['datePresence'] ?? $presence->date_presence,
            'heure_arrivee' => $data['heure_arrivee'] ?? $data['heureArrivee'] ?? $presence->heure_arrivee,
            'heure_depart' => $data['heure_depart'] ?? $data['heureDepart'] ?? $presence->heure_depart,
            'statut' => $data['statut'] ?? $presence->statut,
            'observation' => $data['observation'] ?? $presence->observation,
        ]);
        $presence->load('agent');

        return response()->json([
            'id' => $presence->id,
            'idAgent' => $presence->agent_id,
            'agentNom' => $presence->agent?->nom,
            'agentPrenom' => $presence->agent?->prenom,
            'datePresence' => $presence->date_presence?->format('Y-m-d'),
            'heureArrivee' => $presence->heure_arrivee,
            'heureDepart' => $presence->heure_depart,
            'statut' => $presence->statut,
            'retardMinutes' => $presence->retard_minutes,
            'observation' => $presence->observation,
        ]);
    }

    public function destroy($id)
    {
        Presence::findOrFail($id)->delete();
        return response()->json(['message' => 'Présence supprimée avec succès']);
    }
}
