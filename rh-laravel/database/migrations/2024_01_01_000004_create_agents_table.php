<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('matricule', 100);
            $table->foreignId('grade_id')->constrained('grades');
            $table->foreignId('fonction_id')->constrained('fonctions');
            $table->foreignId('direction_id')->constrained('directions');
            $table->string('nom', 100);
            $table->string('postnom', 100);
            $table->string('prenom', 100);
            $table->string('sexe', 10);
            $table->date('date_naissance');
            $table->string('email', 100)->unique();
            $table->string('telephone', 20);
            $table->string('etat_civil', 20);
            $table->boolean('statut')->default(true);
            $table->string('reference_engagement', 100);
            $table->date('date_engagement');
            $table->string('province', 100);
            $table->string('territoire', 100);
            $table->string('village', 100);
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
