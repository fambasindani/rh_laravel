<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->getAuthPassword())) {
            Log::create([
                'user_id' => $user?->id,
                'action' => 'LOGIN_FAILED',
                'description' => 'Tentative de connexion echouee pour ' . $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        if (!$user->actif) {
            Log::create([
                'user_id' => $user->id,
                'action' => 'LOGIN_FAILED',
                'description' => 'Compte desactive: ' . $user->username,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
            return response()->json(['message' => 'Compte désactivé'], 403);
        }



        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        $roles = $user->roles->pluck('nom_role')->toArray();
        $droits = $user->roles->flatMap(function ($role) {
            return $role->droits->pluck('nom_droit');
        })->unique()->toArray();

        $user->updateQuietly(['last_login' => now()]);

        Log::create([
            'user_id' => $user->id,
            'action' => 'LOGIN',
            'description' => 'Connexion reussie: ' . $user->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return response()->json([
            'token' => $token,
            'type' => 'Bearer',
            'username' => $user->username,
            'roles' => $roles,
            'droits' => $droits,
            'userId' => $user->id,
            'agentId' => $user->agent_id,
        ]);
    }

    public function logout(Request $request)
    {
        Log::create([
            'user_id' => $request->user()->id,
            'action' => 'LOGOUT',
            'description' => 'Deconnexion: ' . $request->user()->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté avec succès']);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $roles = $user->roles->pluck('nom_role')->toArray();
        $droits = $user->roles->flatMap(function ($role) {
            return $role->droits->pluck('nom_droit');
        })->unique()->toArray();

        return response()->json([
            'token' => $request->user()->currentAccessToken()->plainTextToken,
            'type' => 'Bearer',
            'username' => $user->username,
            'roles' => $roles,
            'droits' => $droits,
            'userId' => $user->id,
            'agentId' => $user->agent_id,
        ]);
    }
}
