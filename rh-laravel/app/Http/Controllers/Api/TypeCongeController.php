<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TypeConge;
use Illuminate\Http\Request;

class TypeCongeController extends Controller
{
    public function index(Request $request)
    {
        $query = TypeConge::query();

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($t) => [
            'id' => $t->id,
            'nom' => $t->nom,
            'libelle' => $t->nom,
            'nombreJours' => $t->nombre_jours,
            'dureeMaxJours' => $t->nombre_jours,
            'description' => $t->description,
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
        $typeConges = TypeConge::orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($typeConges->map(fn($t) => [
            'id' => $t->id,
            'nom' => $t->nom,
            'libelle' => $t->nom,
            'nombreJours' => $t->nombre_jours,
            'dureeMaxJours' => $t->nombre_jours,
            'description' => $t->description,
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $nom = $data['nom'] ?? $data['libelle'] ?? null;

        if (!$nom) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                'nom' => ['Le champ nom est obligatoire.'],
            ]], 422);
        }

        $typeConge = TypeConge::create([
            'nom' => $nom,
            'nombre_jours' => $data['nombre_jours'] ?? $data['nombreJours'] ?? $data['dureeMaxJours'] ?? 30,
            'description' => $data['description'] ?? null,
            'statut' => $data['statut'] ?? true,
        ]);

        return response()->json([
            'id' => $typeConge->id,
            'nom' => $typeConge->nom,
            'libelle' => $typeConge->nom,
            'nombreJours' => $typeConge->nombre_jours,
            'dureeMaxJours' => $typeConge->nombre_jours,
            'description' => $typeConge->description,
        ], 201);
    }

    public function show($id)
    {
        $t = TypeConge::findOrFail($id);
        return response()->json([
            'id' => $t->id,
            'nom' => $t->nom,
            'libelle' => $t->nom,
            'nombreJours' => $t->nombre_jours,
            'dureeMaxJours' => $t->nombre_jours,
            'description' => $t->description,
        ]);
    }

    public function update(Request $request, $id)
    {
        $typeConge = TypeConge::findOrFail($id);
        $data = $request->all();

        $typeConge->update([
            'nom' => $data['nom'] ?? $data['libelle'] ?? $typeConge->nom,
            'nombre_jours' => $data['nombre_jours'] ?? $data['nombreJours'] ?? $data['dureeMaxJours'] ?? $typeConge->nombre_jours,
            'description' => $data['description'] ?? $typeConge->description,
        ]);

        return response()->json([
            'id' => $typeConge->id,
            'nom' => $typeConge->nom,
            'libelle' => $typeConge->nom,
            'nombreJours' => $typeConge->nombre_jours,
            'dureeMaxJours' => $typeConge->nombre_jours,
            'description' => $typeConge->description,
        ]);
    }

    public function destroy($id)
    {
        TypeConge::findOrFail($id)->delete();
        return response()->json(['message' => 'Type de congé supprimé avec succès']);
    }
}
