<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('droits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom_droit', 150)->unique();
            $table->string('description', 255)->nullable();
            $table->string('module', 100)->nullable();
            $table->dateTime('date_creation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('droits');
    }
};
