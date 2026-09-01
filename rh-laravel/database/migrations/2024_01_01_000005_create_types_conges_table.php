<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('types_conges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom', 100);
            $table->integer('nombre_jours');
            $table->string('description')->nullable();
            $table->boolean('statut')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('types_conges');
    }
};
