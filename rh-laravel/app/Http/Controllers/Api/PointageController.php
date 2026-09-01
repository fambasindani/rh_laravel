<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Pointage;
use App\Models\ZoneTravail;
use App\Models\HoraireTravail;
use App\Models\JourFerie;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PointageController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agentId'] ?? $data['agent_id'] ?? null;
        $type = strtoupper($data['type'] ?? '');
        $latitude = $data['latitude'] ?? null;
        $longitude = $data['longitude'] ?? null;

        if (!$agentId || !$type) {
            return response()->json(['message' => 'agentId et type sont obligatoires'], 422);
        }

        if ($type !== 'ARRIVEE' && $type !== 'DEPART') {
            return response()->json(['message' => 'Type invalide (ARRIVEE ou DEPART)'], 422);
        }

        if (!$latitude || !$longitude) {
            return response()->json(['message' => 'La localisation GPS est obligatoire'], 422);
        }

        $agent = Agent::find($agentId);
        if (!$agent) {
            return response()->json(['message' => 'Agent non trouvé'], 404);
        }

        $aujourdHui = Carbon::now()->toDateString();

        $dejaPointe = Pointage::where('agent_id', $agentId)
            ->where('date_presence', $aujourdHui)
            ->where('type', $type)
            ->exists();
        if ($dejaPointe) {
            return response()->json(['message' => "Pointage " . strtolower($type) . " déjà effectué aujourd'hui"], 409);
        }

        $estJourFerie = JourFerie::where('date', $aujourdHui)->where('actif', true)->exists();

        $dansLaZone = $this->verifierZone($latitude, $longitude);

        if (!$dansLaZone) {
            $zones = ZoneTravail::where('actif', true)->get();
            $detail = $zones->isEmpty()
                ? 'Aucune zone configurée'
                : $zones->map(function ($z) use ($latitude, $longitude) {
                    $dist = $this->haversine($latitude, $longitude, $z->latitude, $z->longitude);
                    return "Zone '{$z->nom}': distance=" . round($dist) . "m, rayon={$z->rayon}m";
                })->implode(' | ');
            return response()->json([
                'message' => "Hors zone. Vos coords: {$latitude}, {$longitude} — {$detail}",
                'hors_zone' => true,
            ], 403);
        }

        $statut = 'VALIDE';
        $minutesRetard = 0;
        if (!$estJourFerie) {
            $minutesRetard = $this->calculerRetard($agentId, $type);
            $statut = $minutesRetard > 15 ? 'RETARD' : 'VALIDE';
        }

        $photoPath = null;
        if (!empty($data['photoBase64'])) {
            $base64 = $data['photoBase64'];
            if (str_starts_with($base64, 'data:')) {
                $base64 = substr($base64, strpos($base64, ',') + 1);
            }
            $bytes = base64_decode($base64);
            $filename = 'pointage_' . $agentId . '_' . now()->format('Ymd_His') . '.jpg';
            $path = storage_path('app/public/pointages');
            if (!is_dir($path)) mkdir($path, 0755, true);
            file_put_contents($path . '/' . $filename, $bytes);
            $photoPath = '/storage/pointages/' . $filename;
        }

        $zoneFound = ZoneTravail::where('actif', true)->get()->first(function ($z) use ($latitude, $longitude) {
            return $this->haversine($latitude, $longitude, $z->latitude, $z->longitude) <= $z->rayon;
        });

        $pointage = Pointage::create([
            'agent_id' => $agentId,
            'type' => $type,
            'statut' => $statut,
            'horodatage' => now(),
            'date_presence' => $aujourdHui,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'precision_gps' => $data['accuracy'] ?? null,
            'chemin_photo' => $photoPath,
            'infos_appareil' => $data['infosAppareil'] ?? null,
            'id_appareil' => $data['idAppareil'] ?? null,
            'zone_travail_id' => $zoneFound?->id,
            'justification' => $data['justification'] ?? null,
            'minutes_retard' => $minutesRetard,
        ]);

        $message = match ($statut) {
            'VALIDE' => $type === 'ARRIVEE'
                ? "Pointage d'arrivée enregistré avec succès"
                : "Pointage de départ enregistré avec succès",
            'RETARD' => "Pointage enregistré avec {$minutesRetard} minutes de retard",
            default => "Pointage enregistré",
        };

        return response()->json([
            'message' => $message,
            'statut' => $statut,
            'data' => $this->formatPointage($pointage),
        ], 201);
    }

    private function verifierZone($latitude, $longitude)
    {
        $zones = ZoneTravail::where('actif', true)->get();
        if ($zones->isEmpty()) return true;
        return $zones->contains(function ($z) use ($latitude, $longitude) {
            return $this->haversine($latitude, $longitude, $z->latitude, $z->longitude) <= $z->rayon;
        });
    }

    private function calculerRetard($agentId, $type)
    {
        $jourSemaine = Carbon::now()->dayOfWeekIso;
        $horaire = HoraireTravail::where('jour_semaine', $jourSemaine)
            ->where('actif', true)
            ->where(function ($q) use ($agentId) {
                $q->where('agent_id', $agentId)
                  ->orWhereNull('agent_id');
            })
            ->orderByRaw('agent_id DESC')
            ->first();
        if (!$horaire) return 0;

        $heureCible = $type === 'ARRIVEE' ? $horaire->heure_debut : $horaire->heure_fin;
        $maintenant = Carbon::now();
        $target = Carbon::parse($heureCible);
        if ($maintenant->gt($target)) {
            return (int) $target->diffInMinutes($maintenant);
        }
        return 0;
    }

    public function historique(Request $request, $agentId)
    {
        $debut = $request->get('debut', now()->startOfMonth()->toDateString());
        $fin = $request->get('fin', now()->toDateString());

        $pointages = Pointage::with(['zoneTravail'])
            ->where('agent_id', $agentId)
            ->whereBetween('date_presence', [$debut, $fin])
            ->orderByDesc('date_presence')
            ->orderByDesc('horodatage')
            ->get();

        return response()->json([
            'data' => $pointages->map(fn($p) => $this->formatPointage($p)),
        ]);
    }

    public function historiqueBrut(Request $request, $agentId)
    {
        $debut = $request->get('debut', now()->startOfMonth()->toDateString());
        $fin = $request->get('fin', now()->toDateString());

        $pointages = Pointage::where('agent_id', $agentId)
            ->whereBetween('date_presence', [$debut, $fin])
            ->orderByDesc('date_presence')
            ->get();

        return response()->json(['data' => $pointages]);
    }

    public function presencesDuJour(Request $request)
    {
        $today = $request->get('date', now()->toDateString());

        $agentsWithPointage = Pointage::where('date_presence', $today)
            ->pluck('agent_id')
            ->unique();

        if ($agentsWithPointage->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $agents = Agent::with(['grade', 'direction', 'fonction'])
            ->whereIn('id', $agentsWithPointage)
            ->orderByDesc('id')
            ->get();

        $result = $agents->map(function ($agent) use ($today) {
            $pointages = Pointage::where('agent_id', $agent->id)
                ->where('date_presence', $today)
                ->get();

            $arrivee = $pointages->firstWhere('type', 'ARRIVEE');
            $depart = $pointages->firstWhere('type', 'DEPART');

            $heureArrivee = $arrivee?->horodatage ? $arrivee->horodatage->format('H:i:s') : null;
            $heureDepart = $depart?->horodatage ? $depart->horodatage->format('H:i:s') : null;

            $statut = 'ABSENT';
            if ($heureArrivee) {
                $statut = ($arrivee->statut === 'RETARD') ? 'RETARD' : 'PRESENT';
            }

            return [
                'agentId' => $agent->id,
                'agentMatricule' => $agent->matricule,
                'agentNom' => $agent->nom,
                'agentPostnom' => $agent->postnom ?? '',
                'agentPrenom' => $agent->prenom,
                'datePresence' => $today,
                'heureArrivee' => $heureArrivee,
                'heureDepart' => $heureDepart,
                'statut' => $statut,
                'minutesRetard' => $arrivee?->minutes_retard ?? 0,
                'zone' => $arrivee?->zoneTravail?->nom,
                'pointageArrivee' => $arrivee ? [
                    'id' => $arrivee->id,
                    'agentId' => $arrivee->agent_id,
                    'type' => $arrivee->type,
                    'statut' => $arrivee->statut,
                    'horodatage' => $arrivee->horodatage?->format('Y-m-d\TH:i:s'),
                    'nomZone' => $arrivee->zoneTravail?->nom,
                    'minutesRetard' => $arrivee->minutes_retard,
                    'justification' => $arrivee->justification,
                    'photoPath' => $arrivee->chemin_photo,
                ] : null,
                'pointageDepart' => $depart ? [
                    'id' => $depart->id,
                    'agentId' => $depart->agent_id,
                    'type' => $depart->type,
                    'statut' => $depart->statut,
                    'horodatage' => $depart->horodatage?->format('Y-m-d\TH:i:s'),
                    'nomZone' => $depart->zoneTravail?->nom,
                    'minutesRetard' => $depart->minutes_retard,
                    'justification' => $depart->justification,
                    'photoPath' => $depart->chemin_photo,
                ] : null,
            ];
        });

        return response()->json(['data' => $result]);
    }

    public function absencesDuJour(Request $request)
    {
        $today = $request->get('date', now()->toDateString());

        $agentsWithPointage = Pointage::where('date_presence', $today)
            ->pluck('agent_id')
            ->unique();

        if ($agentsWithPointage->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $absents = Agent::with(['grade', 'direction', 'fonction'])
            ->whereNotIn('id', $agentsWithPointage)
            ->orderBy('nom')
            ->get()
            ->map(fn($agent) => [
                'id' => $agent->id,
                'matricule' => $agent->matricule,
                'nom' => $agent->nom,
                'prenom' => $agent->prenom,
                'sexe' => $agent->sexe,
                'direction' => $agent->direction?->nom,
                'grade' => $agent->grade?->sigle,
                'fonction' => $agent->fonction?->nom,
                'statut' => $agent->statut,
            ]);

        return response()->json(['data' => $absents]);
    }

    public function all(Request $request)
    {
        $query = Pointage::with(['agent.grade', 'agent.direction', 'zoneTravail']);

        if ($request->has('date') && $request->date) {
            $query->where('date_presence', $request->date);
        }

        $pointages = $query->orderByDesc('date_presence')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $pointages->map(fn($p) => $this->formatPointage($p)),
        ]);
    }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        return $R * 2 * atan2(sqrt($a), sqrt(1-$a));
    }

    private function formatPointage(Pointage $p)
    {
        return [
            'id' => $p->id,
            'agentId' => $p->agent_id,
            'agentNom' => $p->agent?->nom,
            'agentPrenom' => $p->agent?->prenom,
            'matricule' => $p->agent?->matricule,
            'type' => $p->type,
            'statut' => $p->statut,
            'horodatage' => $p->horodatage?->format('Y-m-d H:i:s'),
            'datePresence' => $p->date_presence?->format('Y-m-d'),
            'latitude' => $p->latitude,
            'longitude' => $p->longitude,
            'nomZone' => $p->zoneTravail?->nom,
            'minutesRetard' => $p->minutes_retard,
            'photoPath' => $p->chemin_photo,
        ];
    }
}
