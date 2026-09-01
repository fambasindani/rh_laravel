<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('intitule')->nullable();
            $table->string('organisme')->nullable();
            $table->string('lieu')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->string('description')->nullable();
            $table->boolean('statut')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
