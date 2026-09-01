<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('sigle', 'like', "%{$keyword}%")
                  ->orWhere('nom', 'like', "%{$keyword}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);
        $grades = $query->orderBy('nom')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'content' => $grades->items(),
            'totalElements' => $grades->total(),
            'totalPages' => $grades->lastPage(),
            'currentPage' => $grades->currentPage(),
            'last' => $grades->currentPage() >= $grades->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sigle' => 'required|string|max:10|unique:grades,sigle',
            'nom' => 'required|string|max:100|unique:grades,nom',
            'statut' => 'required|boolean',
        ]);

        $grade = Grade::create($validated);
        return response()->json($grade, 201);
    }

    public function show($id)
    {
        return response()->json(Grade::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);
        $validated = $request->validate([
            'sigle' => 'sometimes|string|max:10|unique:grades,sigle,' . $id,
            'nom' => 'sometimes|string|max:100|unique:grades,nom,' . $id,
            'statut' => 'sometimes|boolean',
        ]);
        $grade->update($validated);
        return response()->json($grade);
    }

    public function destroy($id)
    {
        Grade::findOrFail($id)->delete();
        return response()->json(['message' => 'Supprimé']);
    }

    public function all()
    {
        return response()->json(Grade::orderBy('nom')->get());
    }

    public function search(Request $request)
    {
        $query = Grade::query();
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('sigle', 'like', "%{$keyword}%")
                  ->orWhere('nom', 'like', "%{$keyword}%");
            });
        }
        return response()->json($query->orderBy('nom')->get());
    }
}
