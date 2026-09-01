<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\FonctionController;
use App\Http\Controllers\Api\DirectionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\DroitController;
use App\Http\Controllers\Api\AbsenceController;
use App\Http\Controllers\Api\AffectationController;
use App\Http\Controllers\Api\AffiliationController;
use App\Http\Controllers\Api\CongeController;
use App\Http\Controllers\Api\ContratController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\FormationController;
use App\Http\Controllers\Api\AgentFormationController;
use App\Http\Controllers\Api\MissionController;
use App\Http\Controllers\Api\PrimeController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\RetraiteController;
use App\Http\Controllers\Api\SanctionController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PresenceController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\HistoriqueConnexionController;
use App\Http\Controllers\Api\TypeCongeController;
use App\Http\Controllers\Api\PointageController;
use App\Http\Controllers\Api\ZoneTravailController;
use App\Http\Controllers\Api\HoraireTravailController;
use App\Http\Controllers\Api\JourFerieController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EtudeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/migrate', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $output = \Illuminate\Support\Facades\Artisan::output();
    return response()->json(['message' => 'Migrations exécutées', 'output' => $output]);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Agents
    Route::get('/agents/all', [AgentController::class, 'all']);
    Route::get('/agents/me', [AgentController::class, 'me']);
    Route::put('/agents/me', [AgentController::class, 'updateMe']);
    Route::put('/agents/me/password', [AgentController::class, 'updatePassword']);
    Route::post('/agents/me/photo', [AgentController::class, 'uploadPhoto']);
    Route::post('/agents/search', [AgentController::class, 'search']);
    Route::post('/agents/with-affiliations', [AgentController::class, 'storeWithAffiliations']);
    Route::put('/agents/{id}/with-affiliations', [AgentController::class, 'updateWithAffiliations']);
    Route::get('/agents/{id}/details', [AgentController::class, 'showDetails']);
    Route::apiResource('agents', AgentController::class)->except(['all']);

    // Grades
    Route::get('/grades/all', [GradeController::class, 'all']);
    Route::post('/grades/search', [GradeController::class, 'search']);
    Route::apiResource('grades', GradeController::class)->except(['all']);

    // Fonctions
    Route::get('/fonctions/all', [FonctionController::class, 'all']);
    Route::post('/fonctions/search', [FonctionController::class, 'search']);
    Route::apiResource('fonctions', FonctionController::class)->except(['all']);

    // Directions
    Route::get('/directions/all', [DirectionController::class, 'all']);
    Route::post('/directions/search', [DirectionController::class, 'search']);
    Route::apiResource('directions', DirectionController::class)->except(['all']);

    // Users (Admin)
    Route::get('/admin/users', [UserController::class, 'index']);
    Route::post('/admin/users', [UserController::class, 'store']);
    Route::put('/admin/users/{id}', [UserController::class, 'update']);
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);

    // Roles
    Route::apiResource('roles', RoleController::class);

    // Droits
    Route::get('/droits/all', [DroitController::class, 'all']);
    Route::post('/droits/init', [DroitController::class, 'initDefaults']);
    Route::get('/droits/role/{roleId}', [DroitController::class, 'getByRoleId']);
    Route::post('/droits/role/{roleId}/bulk', [DroitController::class, 'bulkAssignToRole']);
    Route::apiResource('droits', DroitController::class)->except(['all']);

    // Absences
    Route::get('/absences/all', [AbsenceController::class, 'all']);
    Route::apiResource('absences', AbsenceController::class)->except(['all']);

    // Affectations
    Route::get('/affectations/all', [AffectationController::class, 'all']);
    Route::apiResource('affectations', AffectationController::class)->except(['all']);

    // Affiliations
    Route::get('/affiliations/all', [AffiliationController::class, 'all']);
    Route::post('/affiliations/search', [AffiliationController::class, 'search']);
    Route::apiResource('affiliations', AffiliationController::class)->except(['all']);

    // Conges
    Route::get('/conges/all', [CongeController::class, 'all']);
    Route::patch('/conges/{id}/status', [CongeController::class, 'updateStatus']);
    Route::apiResource('conges', CongeController::class)->except(['all']);

    // Contrats
    Route::get('/contrats/all', [ContratController::class, 'all']);
    Route::apiResource('contrats', ContratController::class)->except(['all']);

    // Documents
    Route::get('/documents/all', [DocumentController::class, 'all']);
    Route::apiResource('documents', DocumentController::class)->except(['all']);

    // Evaluations
    Route::get('/evaluations/all', [EvaluationController::class, 'all']);
    Route::apiResource('evaluations', EvaluationController::class)->except(['all']);

    // Formations
    Route::get('/formations/all', [FormationController::class, 'all']);
    Route::apiResource('formations', FormationController::class)->except(['all']);

    // Agent Formations
    Route::get('/agent-formations/all', [AgentFormationController::class, 'all']);
    Route::apiResource('agent-formations', AgentFormationController::class)->except(['all']);

    // Missions
    Route::get('/missions/all', [MissionController::class, 'all']);
    Route::apiResource('missions', MissionController::class)->except(['all']);

    // Primes
    Route::get('/primes/all', [PrimeController::class, 'all']);
    Route::apiResource('primes', PrimeController::class)->except(['all']);

    // Promotions
    Route::get('/promotions/all', [PromotionController::class, 'all']);
    Route::apiResource('promotions', PromotionController::class)->except(['all']);

    // Retraites
    Route::get('/retraites/all', [RetraiteController::class, 'all']);
    Route::apiResource('retraites', RetraiteController::class)->except(['all']);

    // Sanctions
    Route::get('/sanctions/all', [SanctionController::class, 'all']);
    Route::apiResource('sanctions', SanctionController::class)->except(['all']);

    // Permissions
    Route::get('/permissions/all', [PermissionController::class, 'all']);
    Route::apiResource('permissions', PermissionController::class)->except(['all']);

    // Notifications
    Route::get('/notifications/all', [NotificationController::class, 'all']);
    Route::get('/notifications/agent/{agentId}', [NotificationController::class, 'byAgent']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::apiResource('notifications', NotificationController::class)->except(['all']);

    // Presences
    Route::get('/presences/all', [PresenceController::class, 'all']);
    Route::get('/presences/search', [PresenceController::class, 'search']);
    Route::apiResource('presences', PresenceController::class)->except(['all']);

    // Logs
    Route::get('/admin/logs', [LogController::class, 'index']);

    // Historiques connexions
    Route::get('/historiques-connexions', [HistoriqueConnexionController::class, 'all']);

    // Types conge
    Route::get('/types-conge', [TypeCongeController::class, 'all']);

    // Pointages
    Route::post('/pointages', [PointageController::class, 'store']);
    Route::get('/pointages/historique/{agentId}', [PointageController::class, 'historique']);
    Route::get('/pointages/historique-brut/{agentId}', [PointageController::class, 'historiqueBrut']);
    Route::get('/pointages/presences-du-jour', [PointageController::class, 'presencesDuJour']);
    Route::get('/pointages/absences-du-jour', [PointageController::class, 'absencesDuJour']);
    Route::get('/pointages/all', [PointageController::class, 'all']);

    // Zones de travail
    Route::get('/zones-travail/actives', [ZoneTravailController::class, 'actives']);
    Route::apiResource('zones-travail', ZoneTravailController::class);

    // Horaires de travail
    Route::get('/horaires-travail/agent/{agentId}', [HoraireTravailController::class, 'byAgent']);
    Route::apiResource('horaires-travail', HoraireTravailController::class)->except(['byAgent']);

    // Jours fériés
    Route::apiResource('jours-feries', JourFerieController::class);

    // Dashboard
    Route::get('/statistics/dashboard', [DashboardController::class, 'dashboard']);

    // Études
    Route::get('/etudes/all', [EtudeController::class, 'all']);
    Route::apiResource('etudes', EtudeController::class)->except(['all']);
});

// Diagnostic stockage (temporaire — à supprimer après déploiement)
Route::get('/diagnostic/storage', function () {
    $results = [];
    $results['storage_path'] = storage_path();
    $results['public_path'] = public_path();

    $diskRoot = config('filesystems.disks.public.root');
    $results['disk_root'] = $diskRoot;
    $results['disk_root_exists'] = is_dir($diskRoot);
    $results['disk_root_writable'] = is_writable($diskRoot);

    $uploadDir = $diskRoot . '/uploads/documents';
    $results['upload_dir'] = $uploadDir;
    $results['upload_dir_exists'] = is_dir($uploadDir);

    if (!is_dir($uploadDir)) {
        $results['mkdir_result'] = @mkdir($uploadDir, 0755, true);
        $results['upload_dir_exists_after'] = is_dir($uploadDir);
    }

    $results['upload_dir_writable'] = is_writable($uploadDir);

    $publicStorage = public_path('storage');
    $results['public_storage_exists'] = file_exists($publicStorage);
    $results['public_storage_is_link'] = is_link($publicStorage);
    $results['public_storage_target'] = readlink($publicStorage) ?: 'N/A';

    $testFile = $uploadDir . '/test_' . time() . '.txt';
    $results['test_write'] = @file_put_contents($testFile, 'test');
    if (file_exists($testFile)) {
        $results['test_write_ok'] = true;
        @unlink($testFile);
    } else {
        $results['test_write_ok'] = false;
    }

    return response()->json($results, 200, [
        'Content-Type' => 'application/json',
    ]);
});

// Proxy images (sans auth — pour PDF renderer CORS)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    $mime = mime_content_type($fullPath);
    $content = file_get_contents($fullPath);
    return response($content, 200, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');
