<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Direction;
use App\Models\Grade;
use App\Models\Fonction;
use App\Models\Conge;
use App\Models\Absence;
use App\Models\Notification;
use App\Models\Pointage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $today = now()->toDateString();
        $currentYear = now()->year;

        $totalAgents = Agent::count();
        $totalDirections = Direction::count();
        $totalGrades = Grade::count();
        $totalFonctions = Fonction::count();

        $agentsByDirection = Direction::leftJoin('agents', 'directions.id', '=', 'agents.direction_id')
            ->select('directions.nom as label', DB::raw('count(agents.id) as total'))
            ->groupBy('directions.nom')
            ->orderBy('directions.nom')
            ->pluck('total', 'label')
            ->toArray();

        $agentsByGrade = Grade::leftJoin('agents', 'grades.id', '=', 'agents.grade_id')
            ->select('grades.nom as label', DB::raw('count(agents.id) as total'))
            ->groupBy('grades.nom')
            ->orderBy('grades.nom')
            ->pluck('total', 'label')
            ->toArray();

        $agentsByFonction = Fonction::leftJoin('agents', 'fonctions.id', '=', 'agents.fonction_id')
            ->select('fonctions.nom as label', DB::raw('count(agents.id) as total'))
            ->groupBy('fonctions.nom')
            ->orderBy('fonctions.nom')
            ->pluck('total', 'label')
            ->toArray();

        $agentsByStatut = Agent::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->toArray();

        $agentsBySexe = Agent::select('sexe', DB::raw('count(*) as total'))
            ->groupBy('sexe')
            ->pluck('total', 'sexe')
            ->toArray();

        $rawHire = Agent::whereNotNull('date_engagement')
            ->select(
                DB::raw('YEAR(date_engagement) as year'),
                DB::raw('count(*) as count')
            )
            ->whereYear('date_engagement', '>=', $currentYear - 5)
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('count', 'year')
            ->toArray();

        $hireEvolution = [];
        for ($y = $currentYear - 5; $y <= $currentYear; $y++) {
            $hireEvolution[] = [
                'year' => (int) $y,
                'count' => (int) ($rawHire[$y] ?? 0),
            ];
        }

        $birthdaysByMonth = Agent::whereNotNull('date_naissance')
            ->selectRaw('MONTH(date_naissance) as month, count(*) as count')
            ->groupByRaw('MONTH(date_naissance)')
            ->pluck('count', 'month')
            ->toArray();

        $birthdaysThisYear = [];
        for ($m = 1; $m <= 12; $m++) {
            $birthdaysThisYear[] = [
                'month' => $m,
                'count' => $birthdaysByMonth[$m] ?? 0,
            ];
        }

        $activeAgents = Agent::where('statut', true)->count();
        $inactiveAgents = Agent::where('statut', false)->count();

        $pendingConges = Conge::where('statut', 'EN_ATTENTE')->count();

        $todayPresences = Pointage::where('date_presence', $today)
            ->where('type', 'ARRIVEE')
            ->distinct('agent_id')
            ->count('agent_id');
        $todayAbsences = max(0, $totalAgents - $todayPresences);

        $recentConges = Conge::with('agent')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'agentNom' => $c->agent?->nom,
                'agentPrenom' => $c->agent?->prenom,
                'typeCongeNom' => $c->typeConge?->nom,
                'dateDebut' => $c->date_debut?->format('Y-m-d'),
                'dateFin' => $c->date_fin?->format('Y-m-d'),
                'nombreJours' => $c->nombre_jours,
                'statut' => $c->statut,
            ]);

        $recentAbsences = Absence::with('agent')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'agentNom' => $a->agent?->nom,
                'agentPrenom' => $a->agent?->prenom,
                'dateDebut' => $a->date_debut?->format('Y-m-d'),
                'dateFin' => $a->date_fin?->format('Y-m-d'),
                'motif' => $a->motif,
            ]);

        $recentNotifications = Notification::with('agent')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'message' => $n->message,
                'lu' => $n->lu,
                'dateNotification' => $n->date_notification?->toISOString(),
                'agentEmail' => $n->agent?->email,
            ]);

        return response()->json([
            'totalAgents' => $totalAgents,
            'totalDirections' => $totalDirections,
            'totalGrades' => $totalGrades,
            'totalFonctions' => $totalFonctions,
            'agentsByDirection' => $agentsByDirection,
            'agentsByGrade' => $agentsByGrade,
            'agentsByFonction' => $agentsByFonction,
            'agentsByStatut' => $agentsByStatut,
            'agentsBySexe' => $agentsBySexe,
            'hireEvolution' => $hireEvolution,
            'birthdaysThisYear' => $birthdaysThisYear,
            'activeAgents' => $activeAgents,
            'inactiveAgents' => $inactiveAgents,
            'pendingConges' => $pendingConges,
            'todayAbsences' => $todayAbsences,
            'todayPresences' => $todayPresences,
            'todayPresencesRate' => $totalAgents > 0 ? round(($todayPresences / $totalAgents) * 100) : 0,
            'totalSanctions' => \App\Models\Sanction::count(),
            'unreadNotifications' => Notification::where('lu', false)->count(),
            'recentConges' => $recentConges,
            'recentAbsences' => $recentAbsences,
            'recentNotifications' => $recentNotifications,
        ]);
    }
}
