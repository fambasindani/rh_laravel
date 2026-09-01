<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HoraireTravail;
use Illuminate\Http\Request;

class HoraireTravailController extends Controller
{
    public function index()
    {
        $horaires = HoraireTravail::orderBy('jour_semaine')->get();
        return response()->json(['data' => $horaires->map(fn($h) => $this->format($h))]);
    }

    public function byAgent($agentId)
    {
        $horaires = HoraireTravail::where('agent_id', $agentId)
            ->orderBy('jour_semaine')
            ->get();

        return response()->json(['data' => $horaires->map(fn($h) => $this->format($h))]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validated = [
            'jour_semaine' => $data['jour_semaine'] ?? $data['jourSemaine'] ?? null,
            'heure_debut' => $data['heure_debut'] ?? $data['heureDebut'] ?? null,
            'heure_fin' => $data['heure_fin'] ?? $data['heureFin'] ?? null,
            'debut_fenetre_pointage' => $data['debut_fenetre_pointage'] ?? $data['debutFenetrePointage'] ?? null,
            'fin_fenetre_pointage' => $data['fin_fenetre_pointage'] ?? $data['finFenetrePointage'] ?? null,
            'actif' => $data['actif'] ?? true,
        ];

        $horaire = HoraireTravail::create($validated);
        return response()->json($this->format($horaire), 201);
    }

    public function show($id)
    {
        $horaire = HoraireTravail::findOrFail($id);
        return response()->json($this->format($horaire));
    }

    public function update(Request $request, $id)
    {
        $horaire = HoraireTravail::findOrFail($id);
        $data = $request->all();

        $horaire->update([
            'jour_semaine' => $data['jour_semaine'] ?? $data['jourSemaine'] ?? $horaire->jour_semaine,
            'heure_debut' => $data['heure_debut'] ?? $data['heureDebut'] ?? $horaire->heure_debut,
            'heure_fin' => $data['heure_fin'] ?? $data['heureFin'] ?? $horaire->heure_fin,
            'debut_fenetre_pointage' => $data['debut_fenetre_pointage'] ?? $data['debutFenetrePointage'] ?? $horaire->debut_fenetre_pointage,
            'fin_fenetre_pointage' => $data['fin_fenetre_pointage'] ?? $data['finFenetrePointage'] ?? $horaire->fin_fenetre_pointage,
            'actif' => $data['actif'] ?? $horaire->actif,
        ]);

        return response()->json($this->format($horaire));
    }

    public function destroy($id)
    {
        HoraireTravail::findOrFail($id)->delete();
        return response()->json(['message' => 'Horaire supprimé']);
    }

    private function format(HoraireTravail $h)
    {
        return [
            'id' => $h->id,
            'jourSemaine' => $h->jour_semaine,
            'heureDebut' => $h->heure_debut,
            'heureFin' => $h->heure_fin,
            'debutFenetrePointage' => $h->debut_fenetre_pointage,
            'finFenetrePointage' => $h->fin_fenetre_pointage,
            'actif' => $h->actif,
        ];
    }
}
