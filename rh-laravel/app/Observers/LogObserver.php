<?php

namespace App\Observers;

use App\Models\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogObserver
{
    private $skipModels = ['App\Models\Log'];

    public function created(Model $model)
    {
        $this->log('CREATE', "Creation de " . class_basename($model) . " #{$model->id}", $model);
    }

    public function updated(Model $model)
    {
        $this->log('UPDATE', "Modification de " . class_basename($model) . " #{$model->id}", $model);
    }

    public function deleted(Model $model)
    {
        $this->log('DELETE', "Suppression de " . class_basename($model) . " #{$model->id}", $model);
    }

    private function log(string $action, string $description, Model $model)
    {
        if (in_array(get_class($model), $this->skipModels)) return;

        Log::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
