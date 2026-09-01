<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pointages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('agent_id')->constrained('agents');
            $table->string('type');
            $table->string('statut')->default('VALIDE');
            $table->dateTime('horodatage');
            $table->date('date_presence');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->double('precision_gps')->nullable();
            $table->string('chemin_photo')->nullable();
            $table->string('infos_appareil')->nullable();
            $table->string('id_appareil')->nullable();
            $table->string('adresse_ip')->nullable();
            $table->foreignId('zone_travail_id')->nullable()->constrained('zones_travail');
            $table->string('motif_rejet')->nullable();
            $table->string('justification')->nullable();
            $table->integer('minutes_retard')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};
