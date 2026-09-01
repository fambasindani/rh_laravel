<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('user');

        $search = $request->get('search') ?? $request->get('keyword');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        $logs->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'user' => $log->user ? [
                    'id' => $log->user->id,
                    'username' => $log->user->username,
                ] : null,
                'action' => $log->action,
                'description' => $log->description,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'created_at' => $log->created_at,
            ];
        });

        return response()->json($logs);
    }

    public function all()
    {
        $logs = Log::with('user')->orderBy('created_at', 'desc')->get();

        $logs = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'user' => $log->user ? [
                    'id' => $log->user->id,
                    'username' => $log->user->username,
                ] : null,
                'action' => $log->action,
                'description' => $log->description,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'created_at' => $log->created_at,
            ];
        });

        return response()->json($logs);
    }

    public function show($id)
    {
        $log = Log::with('user')->findOrFail($id);

        return response()->json([
            'id' => $log->id,
            'user' => $log->user ? [
                'id' => $log->user->id,
                'username' => $log->user->username,
            ] : null,
            'action' => $log->action,
            'description' => $log->description,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'created_at' => $log->created_at,
        ]);
    }
}
