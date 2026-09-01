<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('username', 'mwamba@test.com')->with('roles.droits')->first();
if (!$user) { echo "User not found"; exit; }
echo "Roles: " . $user->roles->pluck('nom_role')->implode(', ') . "\n";
echo "Droits: " . $user->roles->flatMap(function($r) { return $r->droits->pluck('nom_droit'); })->unique()->implode(', ') . "\n";
echo "Count: " . $user->roles->flatMap(function($r) { return $r->droits->pluck('nom_droit'); })->unique()->count() . "\n";
