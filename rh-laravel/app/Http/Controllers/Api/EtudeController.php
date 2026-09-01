<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Etude;
use Illuminate\Http\Request;

class EtudeController extends Controller
{
    public function all()
    {
        $etudes = Etude::orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($etudes->map(fn($e) => [
            'id' => $e->id,
            'idAgent' => $e->agent_id,
            'nombreAnnee' => $e->nombre_annee,
            'lieu' => $e->lieu,
            'etablissement' => $e->etablissement,
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_agent' => 'required|exists:agents,id',
            'nombre_annee' => 'required|integer',
            'lieu' => 'required|string|max:255',
            'etablissement' => 'required|string|max:255',
        ]);

        $etude = Etude::create([
            'agent_id' => $validated['id_agent'],
            'nombre_annee' => $validated['nombre_annee'],
            'lieu' => $validated['lieu'],
            'etablissement' => $validated['etablissement'],
        ]);

        return response()->json([
            'id' => $etude->id,
            'idAgent' => $etude->agent_id,
            'nombreAnnee' => $etude->nombre_annee,
            'lieu' => $etude->lieu,
            'etablissement' => $etude->etablissement,
        ], 201);
    }

    public function show($id)
    {
        $etude = Etude::findOrFail($id);
        return response()->json([
            'id' => $etude->id,
            'idAgent' => $etude->agent_id,
            'nombreAnnee' => $etude->nombre_annee,
            'lieu' => $etude->lieu,
            'etablissement' => $etude->etablissement,
        ]);
    }

    public function update(Request $request, $id)
    {
        $etude = Etude::findOrFail($id);

        $validated = $request->validate([
            'id_agent' => 'sometimes|required|exists:agents,id',
            'nombre_annee' => 'sometimes|required|integer',
            'lieu' => 'sometimes|required|string|max:255',
            'etablissement' => 'sometimes|required|string|max:255',
        ]);

        $etude->update([
            'agent_id' => $validated['id_agent'] ?? $etude->agent_id,
            'nombre_annee' => $validated['nombre_annee'] ?? $etude->nombre_annee,
            'lieu' => $validated['lieu'] ?? $etude->lieu,
            'etablissement' => $validated['etablissement'] ?? $etude->etablissement,
        ]);

        return response()->json([
            'id' => $etude->id,
            'idAgent' => $etude->agent_id,
            'nombreAnnee' => $etude->nombre_annee,
            'lieu' => $etude->lieu,
            'etablissement' => $etude->etablissement,
        ]);
    }

    public function destroy($id)
    {
        Etude::findOrFail($id)->delete();
        return response()->json(['message' => 'Étude supprimée']);
    }
}
