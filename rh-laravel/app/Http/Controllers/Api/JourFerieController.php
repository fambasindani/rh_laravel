<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JourFerie;
use Illuminate\Http\Request;

class JourFerieController extends Controller
{
    public function index()
    {
        $jours = JourFerie::orderByDesc('date')->get();
        return response()->json(['data' => $jours]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'date' => 'required|date',
            'actif' => 'nullable|boolean',
        ]);

        $validated['actif'] = $validated['actif'] ?? true;

        $jour = JourFerie::create($validated);

        return response()->json([
            'message' => 'Jour férié créé avec succès',
            'data' => $jour,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $jour = JourFerie::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'date' => 'sometimes|date',
            'actif' => 'nullable|boolean',
        ]);

        $jour->update($validated);

        return response()->json([
            'message' => 'Jour férié mis à jour avec succès',
            'data' => $jour,
        ]);
    }

    public function destroy($id)
    {
        $jour = JourFerie::findOrFail($id);
        $jour->delete();

        return response()->json(['message' => 'Jour férié supprimé avec succès']);
    }
}
