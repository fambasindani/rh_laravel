<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function index(Request $request)
    {
        $query = Formation::query();

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('intitule', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('organisme', 'like', "%{$search}%")
                  ->orWhere('lieu', 'like', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($f) => [
            'id' => $f->id,
            'intitule' => $f->intitule,
            'organisme' => $f->organisme,
            'lieu' => $f->lieu,
            'dateDebut' => $f->date_debut?->format('Y-m-d'),
            'dateFin' => $f->date_fin?->format('Y-m-d'),
            'description' => $f->description,
            'statut' => $f->statut,
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
        $formations = Formation::orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($formations->map(fn($f) => [
            'id' => $f->id,
            'intitule' => $f->intitule,
            'organisme' => $f->organisme,
            'lieu' => $f->lieu,
            'dateDebut' => $f->date_debut?->format('Y-m-d'),
            'dateFin' => $f->date_fin?->format('Y-m-d'),
            'description' => $f->description,
            'statut' => $f->statut,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $intitule = $data['intitule'] ?? null;

        if (!$intitule) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                'intitule' => ['Le champ intitule est obligatoire.'],
            ]], 422);
        }

        $formation = Formation::create([
            'intitule' => $intitule,
            'description' => $data['description'] ?? null,
            'date_debut' => $data['date_debut'] ?? $data['dateDebut'] ?? null,
            'date_fin' => $data['date_fin'] ?? $data['dateFin'] ?? null,
            'lieu' => $data['lieu'] ?? null,
            'organisme' => $data['organisme'] ?? null,
            'statut' => $data['statut'] ?? true,
        ]);

        return response()->json([
            'id' => $formation->id,
            'intitule' => $formation->intitule,
            'organisme' => $formation->organisme,
            'lieu' => $formation->lieu,
            'dateDebut' => $formation->date_debut?->format('Y-m-d'),
            'dateFin' => $formation->date_fin?->format('Y-m-d'),
            'description' => $formation->description,
            'statut' => $formation->statut,
        ], 201);
    }

    public function show($id)
    {
        $f = Formation::findOrFail($id);
        return response()->json([
            'id' => $f->id,
            'intitule' => $f->intitule,
            'organisme' => $f->organisme,
            'lieu' => $f->lieu,
            'dateDebut' => $f->date_debut?->format('Y-m-d'),
            'dateFin' => $f->date_fin?->format('Y-m-d'),
            'description' => $f->description,
            'statut' => $f->statut,
        ]);
    }

    public function update(Request $request, $id)
    {
        $formation = Formation::findOrFail($id);
        $data = $request->all();

        $formation->update([
            'intitule' => $data['intitule'] ?? $formation->intitule,
            'description' => $data['description'] ?? $formation->description,
            'date_debut' => $data['date_debut'] ?? $data['dateDebut'] ?? $formation->date_debut,
            'date_fin' => $data['date_fin'] ?? $data['dateFin'] ?? $formation->date_fin,
            'lieu' => $data['lieu'] ?? $formation->lieu,
            'organisme' => $data['organisme'] ?? $formation->organisme,
            'statut' => $data['statut'] ?? $formation->statut,
        ]);

        return response()->json([
            'id' => $formation->id,
            'intitule' => $formation->intitule,
            'organisme' => $formation->organisme,
            'lieu' => $formation->lieu,
            'dateDebut' => $formation->date_debut?->format('Y-m-d'),
            'dateFin' => $formation->date_fin?->format('Y-m-d'),
            'description' => $formation->description,
            'statut' => $formation->statut,
        ]);
    }

    public function destroy($id)
    {
        Formation::findOrFail($id)->delete();
        return response()->json(['message' => 'Formation supprimée avec succès']);
    }
}
