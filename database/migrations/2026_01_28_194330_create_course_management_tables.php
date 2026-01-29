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
        // 1. Cursos
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Profesor
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image_path')->nullable(); // Portada del curso
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_premium')->default(false); // Para el modelo Freemium
            $table->timestamps();
        });

        // 2. Módulos (Secciones del curso)
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->integer('sort_order')->default(0); // Para ordenar: 1, 2, 3...
            $table->timestamps();
        });

        // 3. Lecciones
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->table('course_modules')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->string('video_url')->nullable(); // YouTube ID o URL
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->boolean('is_free_preview')->default(false); // Hook para usuarios gratis
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Progreso del Estudiante (Gamificación implícita)
        Schema::create('lesson_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->unique(['user_id', 'lesson_id']); // Evitar duplicados
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_user');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
    }
};
