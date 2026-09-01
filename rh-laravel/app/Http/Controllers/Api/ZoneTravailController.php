<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ZoneTravail;
use Illuminate\Http\Request;

class ZoneTravailController extends Controller
{
    public function index()
    {
        $zones = ZoneTravail::orderBy('nom')->get();
        return response()->json(['data' => $zones]);
    }

    public function actives()
    {
        $zones = ZoneTravail::where('actif', true)->orderBy('nom')->get();
        return response()->json(['data' => $zones]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'adresse' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'rayon' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $validated['rayon'] = $validated['rayon'] ?? 100;
        $validated['actif'] = $validated['actif'] ?? true;

        $zone = ZoneTravail::create($validated);

        return response()->json([
            'message' => 'Zone de travail créée avec succès',
            'data' => $zone,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $zone = ZoneTravail::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:100',
            'adresse' => 'nullable|string|max:255',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'rayon' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $zone->update($validated);

        return response()->json([
            'message' => 'Zone de travail mise à jour avec succès',
            'data' => $zone,
        ]);
    }

    public function destroy($id)
    {
        $zone = ZoneTravail::findOrFail($id);
        $zone->delete();

        return response()->json(['message' => 'Zone de travail supprimée avec succès']);
    }
}
