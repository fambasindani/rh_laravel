<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('agent_id')->unique()->constrained('agents');
            $table->string('username', 100)->unique();
            $table->string('password_hash', 255);
            $table->boolean('actif')->default(true);
            $table->dateTime('last_login')->nullable();
            $table->dateTime('date_creation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
