<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_droits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('droit_id')->constrained('droits');
            $table->dateTime('date_attribution');
            $table->unique(['role_id', 'droit_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_droits');
    }
};
