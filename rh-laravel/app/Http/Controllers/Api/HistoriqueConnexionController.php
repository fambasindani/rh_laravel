<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HistoriqueConnexion;
use Illuminate\Http\Request;

class HistoriqueConnexionController extends Controller
{
    public function index(Request $request)
    {
        $query = HistoriqueConnexion::with('user');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('user_agent', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $historiques = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        return response()->json($historiques);
    }

    public function all()
    {
        $historiques = HistoriqueConnexion::with('user')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($historiques);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'ip_address' => 'nullable|string|max:45',
            'user_agent' => 'nullable|string',
            'date_connexion' => 'nullable|date',
            'deconnexion' => 'nullable|date',
            'statut' => 'nullable|string',
        ]);

        $historique = HistoriqueConnexion::create($validated);
        $historique->load('user');

        return response()->json($historique, 201);
    }

    public function show($id)
    {
        $historique = HistoriqueConnexion::with('user')->findOrFail($id);
        return response()->json($historique);
    }

    public function destroy($id)
    {
        $historique = HistoriqueConnexion::findOrFail($id);
        $historique->delete();

        return response()->json(['message' => 'Historique de connexion supprimé avec succès']);
    }
}
