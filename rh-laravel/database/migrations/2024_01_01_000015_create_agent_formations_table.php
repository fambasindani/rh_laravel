<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_formations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('agent_id')->constrained('agents');
            $table->foreignId('formation_id')->constrained('formations');
            $table->string('resultat')->nullable();
            $table->string('observation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_formations');
    }
};
