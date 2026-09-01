<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prime;
use Illuminate\Http\Request;

class PrimeController extends Controller
{
    public function index(Request $request)
    {
        $query = Prime::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('libelle', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($p) => [
            'id' => $p->id,
            'idAgent' => $p->agent_id,
            'agentNom' => $p->agent?->nom,
            'agentPrenom' => $p->agent?->prenom,
            'libelle' => $p->libelle,
            'montant' => $p->montant,
            'datePrime' => $p->date_prime?->format('Y-m-d'),
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
        $primes = Prime::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($primes->map(fn($p) => [
            'id' => $p->id,
            'idAgent' => $p->agent_id,
            'agentNom' => $p->agent?->nom,
            'agentPrenom' => $p->agent?->prenom,
            'libelle' => $p->libelle,
            'montant' => $p->montant,
            'datePrime' => $p->date_prime?->format('Y-m-d'),
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $libelle = $data['libelle'] ?? $data['typePrime'] ?? null;
        $montant = $data['montant'] ?? null;
        $datePrime = $data['date_prime'] ?? $data['datePrime'] ?? null;

        if (!$agentId || !$libelle || !$montant || !$datePrime) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                ...(!$agentId ? ['agent_id' => ['Le champ agent_id est obligatoire.']] : []),
                ...(!$libelle ? ['libelle' => ['Le champ libelle est obligatoire.']] : []),
                ...(!$montant ? ['montant' => ['Le champ montant est obligatoire.']] : []),
                ...(!$datePrime ? ['datePrime' => ['Le champ datePrime est obligatoire.']] : []),
            ]], 422);
        }

        $prime = Prime::create([
            'agent_id' => $agentId,
            'libelle' => $libelle,
            'montant' => $montant,
            'date_prime' => $datePrime,
        ]);
        $prime->load('agent');

        return response()->json([
            'id' => $prime->id,
            'idAgent' => $prime->agent_id,
            'agentNom' => $prime->agent?->nom,
            'agentPrenom' => $prime->agent?->prenom,
            'libelle' => $prime->libelle,
            'montant' => $prime->montant,
            'datePrime' => $prime->date_prime?->format('Y-m-d'),
        ], 201);
    }

    public function show($id)
    {
        $p = Prime::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $p->id,
            'idAgent' => $p->agent_id,
            'agentNom' => $p->agent?->nom,
            'agentPrenom' => $p->agent?->prenom,
            'libelle' => $p->libelle,
            'montant' => $p->montant,
            'datePrime' => $p->date_prime?->format('Y-m-d'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $prime = Prime::findOrFail($id);
        $data = $request->all();

        $prime->update([
            'agent_id' => $data['agent_id'] ?? $data['idAgent'] ?? $prime->agent_id,
            'libelle' => $data['libelle'] ?? $data['typePrime'] ?? $prime->libelle,
            'montant' => $data['montant'] ?? $prime->montant,
            'date_prime' => $data['date_prime'] ?? $data['datePrime'] ?? $prime->date_prime,
        ]);
        $prime->load('agent');

        return response()->json([
            'id' => $prime->id,
            'idAgent' => $prime->agent_id,
            'agentNom' => $prime->agent?->nom,
            'agentPrenom' => $prime->agent?->prenom,
            'libelle' => $prime->libelle,
            'montant' => $prime->montant,
            'datePrime' => $prime->date_prime?->format('Y-m-d'),
        ]);
    }

    public function destroy($id)
    {
        Prime::findOrFail($id)->delete();
        return response()->json(['message' => 'Prime supprimée avec succès']);
    }
}
