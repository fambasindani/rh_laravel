<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affectations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('agent_id')->constrained('agents');
            $table->foreignId('direction_id')->constrained('directions');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};
