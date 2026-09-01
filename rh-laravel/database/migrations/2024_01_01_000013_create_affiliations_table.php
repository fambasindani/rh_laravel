<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('agent_id')->constrained('agents');
            $table->string('nom', 100);
            $table->string('postnom', 100);
            $table->string('prenom', 100)->nullable();
            $table->date('date_naissance');
            $table->string('lieu_naissance', 100);
            $table->string('etat', 10);
            $table->string('relation', 100);
            $table->boolean('statut')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliations');
    }
};
