<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exemplaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livre_id')
                ->constrained('livres')
                ->onDelete('cascade');
            $table->string('code_barre')->unique()->nullable();
            $table->enum('etat', ['neuf','bon','acceptable','mauvais'])
                ->default('bon');
            $table->enum('statut', ['disponible','emprunte','reserve','perdu','retire'])
                ->default('disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exemplaires');
    }
};
