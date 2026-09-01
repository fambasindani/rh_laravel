<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('agent_id')->constrained('agents');
            $table->foreignId('type_conge_id')->constrained('types_conges');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->integer('nombre_jours');
            $table->string('motif')->nullable();
            $table->string('statut')->default('EN_ATTENTE');
            $table->string('observation')->nullable();
            $table->dateTime('date_demande');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conges');
    }
};
