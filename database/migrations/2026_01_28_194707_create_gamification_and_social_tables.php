<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Logros / Medallas definibles por el Admin
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ej: "Estudiante Dedicado"
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon_path')->nullable(); // Imagen de la medalla
            $table->integer('points_reward')->default(0); // Puntos extra por ganar la medalla
            $table->timestamps();
        });

        // 2. Asignación de Logros a Usuarios
        Schema::create('achievement_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
            $table->timestamp('awarded_at')->useCurrent();
        });

        // 3. Historial de Puntos (Ledger)
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('amount'); // Puede ser positivo o negativo
            $table->string('description'); // Ej: "Completó Curso Laravel"
            $table->morphs('reference'); // Polimórfico: Puede referenciar a un Course, Lesson, o Comment
            $table->timestamps();
        });

        // 4. Comentarios Unificados (Polimórfico)
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('commentable'); // Crea commentable_id y commentable_type
            $table->text('body');
            $table->boolean('is_approved')->default(true); // Moderación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('achievement_user');
        Schema::dropIfExists('achievements');
    }
};
