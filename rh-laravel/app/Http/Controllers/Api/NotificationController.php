<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::with('agent');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhereHas('agent', function ($aq) use ($search) {
                      $aq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%");
                  });
            });
        }

        $paginator = $query->orderBy('created_at', 'desc')->orderByDesc('id')->paginate($request->get('per_page', 15));

        $formatted = $paginator->getCollection()->map(fn($n) => [
            'id' => $n->id,
            'agentId' => $n->agent_id,
            'agentNom' => $n->agent?->nom,
            'agentPrenom' => $n->agent?->prenom,
            'message' => $n->message,
            'lu' => $n->lu,
            'dateNotification' => $n->date_notification?->format('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'data' => $formatted,
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    public function all()
    {
        $notifications = Notification::with('agent')->orderBy('created_at', 'desc')->orderByDesc('id')->get();
        return response()->json($notifications->map(fn($n) => [
            'id' => $n->id,
            'agentId' => $n->agent_id,
            'agentNom' => $n->agent?->nom,
            'agentPrenom' => $n->agent?->prenom,
            'message' => $n->message,
            'lu' => $n->lu,
            'dateNotification' => $n->date_notification?->format('Y-m-d H:i:s'),
        ]));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $agentId = $data['agent_id'] ?? $data['agentId'] ?? null;
        $message = $data['message'] ?? null;

        if (!$message) {
            return response()->json(['message' => 'Validation échouée', 'errors' => [
                'message' => ['Le champ message est obligatoire.'],
            ]], 422);
        }

        $notification = Notification::create([
            'agent_id' => $agentId,
            'message' => $message,
            'lu' => $data['lu'] ?? false,
            'date_notification' => now(),
        ]);
        $notification->load('agent');

        return response()->json([
            'id' => $notification->id,
            'agentId' => $notification->agent_id,
            'agentNom' => $notification->agent?->nom,
            'agentPrenom' => $notification->agent?->prenom,
            'message' => $notification->message,
            'lu' => $notification->lu,
            'dateNotification' => $notification->date_notification?->format('Y-m-d H:i:s'),
        ], 201);
    }

    public function show($id)
    {
        $n = Notification::with('agent')->findOrFail($id);
        return response()->json([
            'id' => $n->id,
            'agentId' => $n->agent_id,
            'agentNom' => $n->agent?->nom,
            'agentPrenom' => $n->agent?->prenom,
            'message' => $n->message,
            'lu' => $n->lu,
            'dateNotification' => $n->date_notification?->format('Y-m-d H:i:s'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $data = $request->all();

        $notification->update([
            'agent_id' => $data['agent_id'] ?? $data['agentId'] ?? $notification->agent_id,
            'message' => $data['message'] ?? $notification->message,
            'lu' => $data['lu'] ?? $notification->lu,
        ]);
        $notification->load('agent');

        return response()->json([
            'id' => $notification->id,
            'agentId' => $notification->agent_id,
            'agentNom' => $notification->agent?->nom,
            'agentPrenom' => $notification->agent?->prenom,
            'message' => $notification->message,
            'lu' => $notification->lu,
            'dateNotification' => $notification->date_notification?->format('Y-m-d H:i:s'),
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['lu' => true, 'date_notification' => now()]);
        $notification->load('agent');

        return response()->json([
            'id' => $notification->id,
            'agentId' => $notification->agent_id,
            'message' => $notification->message,
            'lu' => $notification->lu,
            'dateNotification' => $notification->date_notification?->format('Y-m-d H:i:s'),
        ]);
    }

    public function byAgent($agentId)
    {
        $notifications = Notification::with('agent')
            ->where('agent_id', $agentId)
            ->orderBy('created_at', 'desc')
            ->orderByDesc('id')
            ->get();

        return response()->json($notifications->map(fn($n) => [
            'id' => $n->id,
            'agentId' => $n->agent_id,
            'agentNom' => $n->agent?->nom,
            'agentPrenom' => $n->agent?->prenom,
            'message' => $n->message,
            'lu' => $n->lu,
            'dateNotification' => $n->date_notification?->format('Y-m-d H:i:s'),
        ]));
    }

    public function destroy($id)
    {
        Notification::findOrFail($id)->delete();
        return response()->json(['message' => 'Notification supprimée avec succès']);
    }
}
