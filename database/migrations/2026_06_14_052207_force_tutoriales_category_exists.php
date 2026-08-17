<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $exists = DB::table('categories')->where('slug', 'tutoriales')->exists();
        if (! $exists) {
            DB::table('categories')->insert([
                'name' => 'Tutoriales',
                'slug' => 'tutoriales',
                'description' => 'Guías paso a paso para aprender tecnologías del futuro.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se elimina la categoría para evitar pérdida de datos en producción y rotura de claves foráneas.
    }
};
