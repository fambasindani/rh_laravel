<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private function toPublicPath(?string $path): ?string
    {
        if (!$path) return null;
        if (str_starts_with($path, '/')) return '/api' . $path;
        return '/api/storage/' . ltrim($path, '/');
    }

    public function index(Request $request)
    {
        $query = Document::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('intitule', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        return response()->json($documents);
    }

    public function all()
    {
        $documents = Document::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($documents);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['idAgent'] ?? null;
        $intitule = $data['intitule'] ?? $data['titre'] ?? null;

        if (!$agentId) {
            return response()->json([
                'message' => 'Validation échouée',
                'errors' => ['agent_id' => ['Le champ agent_id est obligatoire.']]
            ], 422);
        }
        if (!$intitule) {
            return response()->json([
                'message' => 'Validation échouée',
                'errors' => ['intitule' => ['Le champ intitule est obligatoire.']]
            ], 422);
        }

        $path = null;
        if ($request->hasFile('fichier')) {
            $uploadDir = config('filesystems.disks.public.root') . '/uploads/documents';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            $file = $request->file('fichier');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/documents', $filename, 'public');
            if (!$path) {
                return response()->json([
                    'message' => 'Erreur lors de l\'enregistrement du fichier',
                    'errors' => ['fichier' => ['Impossible de sauvegarder le fichier sur le serveur.']]
                ], 500);
            }
        }

        $document = Document::create([
            'agent_id' => $agentId,
            'intitule' => $intitule,
            'chemin_fichier' => $path,
        ]);
        $document->load('agent');

        return response()->json([
            'id' => $document->id,
            'idAgent' => $document->agent_id,
            'intitule' => $document->intitule,
            'cheminFichier' => $this->toPublicPath($document->chemin_fichier),
        ], 201);
    }

    public function show($id)
    {
        $document = Document::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $document->id,
            'idAgent' => $document->agent_id,
            'intitule' => $document->intitule,
            'cheminFichier' => $this->toPublicPath($document->chemin_fichier),
        ]);
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $data = $request->all();

        $intitule = $data['intitule'] ?? $data['titre'] ?? $document->intitule;

        if ($request->hasFile('fichier')) {
            if ($document->chemin_fichier && Storage::disk('public')->exists($document->chemin_fichier)) {
                Storage::disk('public')->delete($document->chemin_fichier);
            }
            $uploadDir = config('filesystems.disks.public.root') . '/uploads/documents';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            $file = $request->file('fichier');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/documents', $filename, 'public');
            $document->chemin_fichier = $path;
        }

        $document->intitule = $intitule;
        $document->save();
        $document->load('agent');

        return response()->json([
            'id' => $document->id,
            'idAgent' => $document->agent_id,
            'intitule' => $document->intitule,
            'cheminFichier' => $this->toPublicPath($document->chemin_fichier),
        ]);
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        if ($document->chemin_fichier && Storage::disk('public')->exists($document->chemin_fichier)) {
            Storage::disk('public')->delete($document->chemin_fichier);
        }

        $document->delete();

        return response()->json(['message' => 'Document supprimé avec succès']);
    }
}
