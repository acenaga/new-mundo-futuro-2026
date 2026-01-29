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
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('email');
            $table->json('social_links')->nullable()->after('bio');

            // Gamificación: Caché de puntos totales para evitar consultas pesadas
            $table->unsignedBigInteger('current_points')->default(0)->after('social_links');
            // Gamificación: Nivel actual (calculado en base a puntos)
            $table->unsignedInteger('current_level')->default(1)->after('current_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'social_links', 'current_points', 'current_level']);
        });
    }
};
