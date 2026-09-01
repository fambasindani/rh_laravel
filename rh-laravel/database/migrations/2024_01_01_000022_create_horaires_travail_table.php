<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horaires_travail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('agent_id')->nullable()->constrained('agents');
            $table->integer('jour_semaine');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->time('debut_fenetre_pointage');
            $table->time('fin_fenetre_pointage');
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horaires_travail');
    }
};
