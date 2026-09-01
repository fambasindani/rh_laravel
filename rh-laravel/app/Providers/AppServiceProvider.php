<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Observers\LogObserver;
use App\Models\Agent;
use App\Models\Conge;
use App\Models\Grade;
use App\Models\Fonction;
use App\Models\Direction;
use App\Models\User;
use App\Models\Role;
use App\Models\Droit;
use App\Models\Formation;
use App\Models\Notification;
use App\Models\Sanction;
use App\Models\Absence;
use App\Models\Permission;
use App\Models\Mission;
use App\Models\Evaluation;
use App\Models\Prime;
use App\Models\Contrat;
use App\Models\Presence;
use App\Models\TypeConge;
use App\Models\Retraite;
use App\Models\Promotion;
use App\Models\Affiliation;
use App\Models\Affectation;
use App\Models\AgentFormation;
use App\Models\Document;
use App\Models\Etude;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $models = [
            Agent::class, Conge::class, Grade::class, Fonction::class, Direction::class,
            User::class, Role::class, Droit::class, Formation::class, Notification::class,
            Sanction::class, Absence::class, Permission::class, Mission::class,
            Evaluation::class, Prime::class, Contrat::class, Presence::class,
            TypeConge::class, Retraite::class, Promotion::class, Affiliation::class,
            Affectation::class, AgentFormation::class, Document::class, Etude::class,
        ];

        foreach ($models as $model) {
            $model::observe(LogObserver::class);
        }
    }
}
