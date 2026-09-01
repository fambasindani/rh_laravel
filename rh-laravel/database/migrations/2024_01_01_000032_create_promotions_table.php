<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('agent_id')->constrained('agents');
            $table->foreignId('grade_id')->constrained('grades');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('reference', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
