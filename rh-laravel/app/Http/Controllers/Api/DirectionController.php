<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Direction;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Direction::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nom', 'like', "%{$keyword}%")
                  ->orWhere('sigle', 'like', "%{$keyword}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);
        $directions = $query->orderBy('nom')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'content' => $directions->items(),
            'totalElements' => $directions->total(),
            'totalPages' => $directions->lastPage(),
            'currentPage' => $directions->currentPage(),
            'last' => $directions->currentPage() >= $directions->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sigle' => 'required|string|unique:directions,sigle',
            'nom' => 'required|string|unique:directions,nom',
            'statut' => 'required|boolean',
        ]);

        $direction = Direction::create($validated);
        return response()->json($direction, 201);
    }

    public function show($id)
    {
        return response()->json(Direction::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $direction = Direction::findOrFail($id);
        $validated = $request->validate([
            'sigle' => 'sometimes|string|unique:directions,sigle,' . $id,
            'nom' => 'sometimes|string|unique:directions,nom,' . $id,
            'statut' => 'sometimes|boolean',
        ]);
        $direction->update($validated);
        return response()->json($direction);
    }

    public function destroy($id)
    {
        Direction::findOrFail($id)->delete();
        return response()->json(['message' => 'Supprimé']);
    }

    public function all()
    {
        return response()->json(Direction::orderBy('nom')->get());
    }

    public function search(Request $request)
    {
        $query = Direction::query();
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nom', 'like', "%{$keyword}%")
                  ->orWhere('sigle', 'like', "%{$keyword}%");
            });
        }
        return response()->json($query->orderBy('nom')->get());
    }
}
