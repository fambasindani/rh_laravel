<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fonction;
use Illuminate\Http\Request;

class FonctionController extends Controller
{
    public function index(Request $request)
    {
        $query = Fonction::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('nom', 'like', "%{$keyword}%");
        }

        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);
        $fonctions = $query->orderBy('nom')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'content' => $fonctions->items(),
            'totalElements' => $fonctions->total(),
            'totalPages' => $fonctions->lastPage(),
            'currentPage' => $fonctions->currentPage(),
            'last' => $fonctions->currentPage() >= $fonctions->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:fonctions,nom',
            'statut' => 'required|boolean',
        ]);

        $fonction = Fonction::create($validated);
        return response()->json($fonction, 201);
    }

    public function show($id)
    {
        return response()->json(Fonction::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $fonction = Fonction::findOrFail($id);
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255|unique:fonctions,nom,' . $id,
            'statut' => 'sometimes|boolean',
        ]);
        $fonction->update($validated);
        return response()->json($fonction);
    }

    public function destroy($id)
    {
        Fonction::findOrFail($id)->delete();
        return response()->json(['message' => 'Supprimé']);
    }

    public function all()
    {
        return response()->json(Fonction::orderBy('nom')->get());
    }

    public function search(Request $request)
    {
        $query = Fonction::query();
        if ($request->filled('keyword')) {
            $query->where('nom', 'like', "%{$request->keyword}%");
        }
        return response()->json($query->orderBy('nom')->get());
    }
}
