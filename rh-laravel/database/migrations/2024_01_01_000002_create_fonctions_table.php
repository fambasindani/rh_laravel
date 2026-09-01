<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fonctions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom', 255)->unique();
            $table->boolean('statut')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fonctions');
    }
};
