<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('primes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('agent_id')->constrained('agents');
            $table->string('libelle', 150);
            $table->decimal('montant', 18, 2);
            $table->date('date_prime');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('primes');
    }
};
