<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgentController extends Controller
{
    private function formatAgent(Agent $agent)
    {
        return [
            'id' => $agent->id,
            'matricule' => $agent->matricule,
            'nom' => $agent->nom,
            'postnom' => $agent->postnom,
            'prenom' => $agent->prenom,
            'sexe' => $agent->sexe,
            'dateNaissance' => $agent->date_naissance?->format('Y-m-d'),
            'email' => $agent->email,
            'telephone' => $agent->telephone,
            'etatCivil' => $agent->etat_civil,
            'statut' => $agent->statut,
            'referenceEngagement' => $agent->reference_engagement,
            'dateEngagement' => $agent->date_engagement?->format('Y-m-d'),
            'province' => $agent->province,
            'territoire' => $agent->territoire,
            'village' => $agent->village,
            'photo' => $agent->photo,
            'idGrade' => $agent->grade_id,
            'gradeSigle' => $agent->grade?->sigle,
            'gradeNom' => $agent->grade?->nom,
            'idFonction' => $agent->fonction_id,
            'fonctionNom' => $agent->fonction?->nom,
            'idDirection' => $agent->direction_id,
            'directionSigle' => $agent->direction?->sigle,
            'directionNom' => $agent->direction?->nom,
        ];
    }

    public function index(Request $request)
    {
        $query = Agent::with(['grade', 'fonction', 'direction']);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nom', 'like', "%{$keyword}%")
                  ->orWhere('postnom', 'like', "%{$keyword}%")
                  ->orWhere('prenom', 'like', "%{$keyword}%")
                  ->orWhere('matricule', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('gradeId')) $query->where('grade_id', $request->gradeId);
        if ($request->filled('fonctionId')) $query->where('fonction_id', $request->fonctionId);
        if ($request->filled('directionId')) $query->where('direction_id', $request->directionId);
        if ($request->filled('statut')) $query->where('statut', $request->boolean('statut'));

        $perPage = $request->input('per_page', $request->input('size', 10));
        $page = $request->input('page', 1);
        $agents = $query->orderBy('nom')->paginate($perPage, ['*'], 'page', $page);

        $formatted = $agents->getCollection()->map(fn($a) => $this->formatAgent($a));

        return response()->json([
            'data' => $formatted,
            'current_page' => $agents->currentPage(),
            'per_page' => $agents->perPage(),
            'total' => $agents->total(),
            'last_page' => $agents->lastPage(),
        ]);
    }

    private function parseAgentPayload(Request $request): array
    {
        $contentType = $request->header('Content-Type', '');
        $raw = $request->getContent();

        // For multipart/form-data with plain text 'agent' field (not Blob),
        // PHP puts the value in $_POST. $request->input('agent') returns the JSON string.
        // $request->getContent() may be empty on Windows due to TransformsRequest consuming php://input.
        $agentInput = $request->input('agent');
        if (is_array($agentInput)) {
            return $agentInput;
        }
        if (is_string($agentInput) && !empty($agentInput)) {
            $fixed = mb_convert_encoding($agentInput, 'UTF-8', ['ASCII', 'UTF-8', 'ISO-8859-1', 'Windows-1252']);
            $decoded = json_decode($fixed, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Try raw body for JSON requests
        if (!empty($raw) && (strpos($contentType, 'application/json') !== false || strpos($contentType, 'text/plain') !== false)) {
            $fixed = mb_convert_encoding($raw, 'UTF-8', ['ASCII', 'UTF-8', 'ISO-8859-1', 'Windows-1252']);
            $decoded = json_decode($fixed, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Try multipart boundary parsing as last resort
        if (!empty($raw) && strpos($contentType, 'multipart/form-data') !== false && preg_match('/boundary=([^\s;]+)/', $contentType, $bm)) {
            $boundary = trim($bm[1]);
            $parts = explode('--' . $boundary, $raw);
            foreach ($parts as $part) {
                if (strpos($part, 'name="agent"') !== false) {
                    $segments = preg_split('/\r?\n\r?\n/', $part, 2);
                    if (isset($segments[1])) {
                        $body = trim(rtrim($segments[1], "\r\n"));
                        $fixed = mb_convert_encoding($body, 'UTF-8', ['ASCII', 'UTF-8', 'ISO-8859-1', 'Windows-1252']);
                        $decoded = json_decode($fixed, true);
                        if (is_array($decoded)) {
                            return $decoded;
                        }
                    }
                }
            }
        }

        $all = $request->all();
        if (!empty($all)) {
            return $all;
        }

        return [];
    }

    private function handlePhotoUpload(Request $request, Agent $agent): void
    {
        if ($request->hasFile('photo')) {
            if ($agent->photo) {
                $oldPath = ltrim($agent->photo, '/storage/');
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('photo')->store('agents/photos', 'public');
            $agent->update(['photo' => '/storage/' . $path]);
        }
    }

    public function store(Request $request)
    {
        $input = $this->parseAgentPayload($request);

        $v = \Validator::make($input, [
            'matricule' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'postnom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'sexe' => 'required|string|max:10',
            'dateNaissance' => 'sometimes|date',
            'date_naissance' => 'sometimes|date',
            'email' => 'required|email|max:100',
            'telephone' => 'required|string|max:20',
            'etatCivil' => 'sometimes|string|max:20',
            'etat_civil' => 'sometimes|string|max:20',
            'statut' => 'required|boolean',
            'referenceEngagement' => 'sometimes|string|max:100',
            'reference_engagement' => 'sometimes|string|max:100',
            'dateEngagement' => 'sometimes|date',
            'date_engagement' => 'sometimes|date',
            'province' => 'sometimes|string|max:100',
            'territoire' => 'sometimes|string|max:100',
            'village' => 'sometimes|string|max:100',
            'idGrade' => 'sometimes|exists:grades,id',
            'grade_id' => 'sometimes|exists:grades,id',
            'idFonction' => 'sometimes|exists:fonctions,id',
            'fonction_id' => 'sometimes|exists:fonctions,id',
            'idDirection' => 'sometimes|exists:directions,id',
            'direction_id' => 'sometimes|exists:directions,id',
        ])->validated();

        $get = function ($camel, $snake) use ($v) {
            return $v[$camel] ?? $v[$snake] ?? null;
        };

        $matricule = $get('matricule', 'matricule');
        if ($matricule && strtoupper($matricule) !== 'NU') {
            $exists = Agent::where('matricule', $matricule)->exists();
            if ($exists) {
                return response()->json(['message' => "Le matricule '{$matricule}' existe déjà"], 422);
            }
        }

        $data = [
            'matricule' => $get('matricule', 'matricule'),
            'nom' => $get('nom', 'nom'),
            'postnom' => $get('postnom', 'postnom'),
            'prenom' => $get('prenom', 'prenom'),
            'sexe' => $get('sexe', 'sexe'),
            'date_naissance' => $get('dateNaissance', 'date_naissance'),
            'email' => $get('email', 'email'),
            'telephone' => $get('telephone', 'telephone'),
            'etat_civil' => $get('etatCivil', 'etat_civil'),
            'statut' => $get('statut', 'statut'),
            'reference_engagement' => $get('referenceEngagement', 'reference_engagement'),
            'date_engagement' => $get('dateEngagement', 'date_engagement'),
            'province' => $get('province', 'province'),
            'territoire' => $get('territoire', 'territoire'),
            'village' => $get('village', 'village'),
            'grade_id' => $get('idGrade', 'grade_id'),
            'fonction_id' => $get('idFonction', 'fonction_id'),
            'direction_id' => $get('idDirection', 'direction_id'),
        ];

        $agent = Agent::create($data);
        $this->handlePhotoUpload($request, $agent);
        $agent->load(['grade', 'fonction', 'direction']);

        return response()->json($this->formatAgent($agent), 201);
    }

    public function show($id)
    {
        $agent = Agent::with(['grade', 'fonction', 'direction'])->findOrFail($id);
        return response()->json($this->formatAgent($agent));
    }

    public function showDetails($id)
    {
        $agent = Agent::with([
            'grade', 'fonction', 'direction',
            'affectations.direction', 'promotions.grade', 'affiliations',
            'etudes', 'documents',
        ])->findOrFail($id);

        $data = $this->formatAgent($agent);
        $data['affectations'] = $agent->affectations->map(fn($a) => [
            'id' => $a->id,
            'idDirection' => $a->direction_id,
            'directionSigle' => $a->direction?->sigle,
            'directionNom' => $a->direction?->nom,
            'dateDebut' => $a->date_debut ? (is_string($a->date_debut) ? $a->date_debut : $a->date_debut->format('Y-m-d')) : null,
            'dateFin' => $a->date_fin ? (is_string($a->date_fin) ? $a->date_fin : $a->date_fin->format('Y-m-d')) : null,
        ]);
        $data['promotions'] = $agent->promotions->map(fn($p) => [
            'id' => $p->id,
            'idGrade' => $p->grade_id,
            'gradeSigle' => $p->grade?->sigle,
            'gradeNom' => $p->grade?->nom,
            'dateDebut' => $p->date_debut ? (is_string($p->date_debut) ? $p->date_debut : $p->date_debut->format('Y-m-d')) : null,
            'dateFin' => $p->date_fin ? (is_string($p->date_fin) ? $p->date_fin : $p->date_fin->format('Y-m-d')) : null,
            'reference' => $p->reference,
        ]);
        $data['affiliations'] = $agent->affiliations->map(fn($a) => [
            'id' => $a->id,
            'nom' => $a->nom,
            'postnom' => $a->postnom,
            'prenom' => $a->prenom,
            'dateNaissance' => $a->date_naissance ? (is_string($a->date_naissance) ? $a->date_naissance : $a->date_naissance->format('Y-m-d')) : null,
            'lieuNaissance' => $a->lieu_naissance,
            'etat' => $a->etat,
            'relation' => $a->relation,
            'statut' => $a->statut,
        ]);
        $data['etudes'] = $agent->etudes->map(fn($e) => [
            'id' => $e->id,
            'nombreAnnee' => $e->nombre_annee,
            'lieu' => $e->lieu,
            'etablissement' => $e->etablissement,
        ]);
        $data['documents'] = $agent->documents->map(fn($d) => [
            'id' => $d->id,
            'intitule' => $d->intitule,
            'cheminFichier' => str_starts_with($d->chemin_fichier, '/') || str_starts_with($d->chemin_fichier, 'http')
                ? $d->chemin_fichier
                : '/storage/' . ltrim($d->chemin_fichier, '/'),
        ]);

        return response()->json($data);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $agent = Agent::with(['grade', 'fonction', 'direction', 'affectations', 'promotions'])
            ->findOrFail($user->agent_id);
        return response()->json($this->formatAgent($agent));
    }

    public function update(Request $request, $id)
    {
        $agent = Agent::findOrFail($id);
        $input = $this->parseAgentPayload($request);

        $fields = [
            'matricule' => 'sometimes|string|max:100',
            'nom' => 'sometimes|string|max:100',
            'postnom' => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'sexe' => 'sometimes|string|max:10',
            'dateNaissance' => 'sometimes|date',
            'date_naissance' => 'sometimes|date',
            'email' => 'sometimes|email|max:100|unique:agents,email,' . $id,
            'telephone' => 'sometimes|string|max:20',
            'etatCivil' => 'sometimes|string|max:20',
            'etat_civil' => 'sometimes|string|max:20',
            'statut' => 'sometimes|boolean',
            'referenceEngagement' => 'sometimes|string|max:100',
            'reference_engagement' => 'sometimes|string|max:100',
            'dateEngagement' => 'sometimes|date',
            'date_engagement' => 'sometimes|date',
            'province' => 'sometimes|string|max:100',
            'territoire' => 'sometimes|string|max:100',
            'village' => 'sometimes|string|max:100',
            'idGrade' => 'sometimes|exists:grades,id',
            'grade_id' => 'sometimes|exists:grades,id',
            'idFonction' => 'sometimes|exists:fonctions,id',
            'fonction_id' => 'sometimes|exists:fonctions,id',
            'idDirection' => 'sometimes|exists:directions,id',
            'direction_id' => 'sometimes|exists:directions,id',
        ];

        $validated = \Validator::make($input, $fields)->validated();

        $map = [
            'dateNaissance' => 'date_naissance',
            'etatCivil' => 'etat_civil',
            'referenceEngagement' => 'reference_engagement',
            'dateEngagement' => 'date_engagement',
            'idGrade' => 'grade_id',
            'idFonction' => 'fonction_id',
            'idDirection' => 'direction_id',
        ];

        $data = [];
        foreach ($validated as $key => $value) {
            if (isset($map[$key])) {
                $data[$map[$key]] = $value;
            } elseif (!array_key_exists($key, $map)) {
                $data[$key] = $value;
            }
        }

        $agent->update($data);
        $this->handlePhotoUpload($request, $agent);
        $agent->load(['grade', 'fonction', 'direction']);

        return response()->json($this->formatAgent($agent));
    }

    public function updateMe(Request $request)
    {
        $user = $request->user();
        $agent = Agent::findOrFail($user->agent_id);

        $validated = $request->validate([
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100|unique:agents,email,' . $agent->id,
        ]);

        $agent->update($validated);
        $agent->load(['grade', 'fonction', 'direction']);

        return response()->json($this->formatAgent($agent));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:6',
        ]);

        $user = $request->user();

        if (!\Hash::check($request->currentPassword, $user->getAuthPassword())) {
            return response()->json(['message' => 'Mot de passe actuel incorrect'], 422);
        }

        $user->update(['password_hash' => \Hash::make($request->newPassword)]);

        return response()->json(['message' => 'Mot de passe modifié avec succès']);
    }

    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->delete();
        return response()->json(['message' => 'Agent supprimé avec succès']);
    }

    public function search(Request $request)
    {
        $query = Agent::with(['grade', 'fonction', 'direction']);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nom', 'like', "%{$keyword}%")
                  ->orWhere('postnom', 'like', "%{$keyword}%")
                  ->orWhere('prenom', 'like', "%{$keyword}%")
                  ->orWhere('matricule', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('gradeId')) $query->where('grade_id', $request->gradeId);
        if ($request->filled('fonctionId')) $query->where('fonction_id', $request->fonctionId);
        if ($request->filled('directionId')) $query->where('direction_id', $request->directionId);
        if ($request->has('statut')) $query->where('statut', $request->boolean('statut'));

        $perPage = (int) $request->input('per_page', $request->input('size', 10));
        $currentPage = (int) $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $total = $query->count();
        $agents = $query->orderBy('nom')->skip($offset)->take($perPage)->get();
        $totalPages = (int) ceil($total / $perPage);

        return response()->json([
            'data' => $agents->map(fn($a) => $this->formatAgent($a)),
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $totalPages,
        ]);
    }

    public function all()
    {
        $agents = Agent::with(['grade', 'fonction', 'direction'])
            ->orderBy('nom')
            ->get()
            ->map(fn($a) => $this->formatAgent($a));

        return response()->json($agents);
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|max:2048']);

        $user = $request->user();
        $agent = Agent::findOrFail($user->agent_id);

        if ($agent->photo) {
            $oldPath = ltrim($agent->photo, '/storage/');
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('photo')->store('agents/photos', 'public');
        $photoUrl = '/storage/' . $path;
        $agent->update(['photo' => $photoUrl]);

        return response()->json(['photo' => $photoUrl]);
    }

    public function storeWithAffiliations(Request $request) { return $this->store($request); }
    public function updateWithAffiliations(Request $request, $id) { return $this->update($request, $id); }
}
