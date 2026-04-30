<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tutoriales', 'slug' => 'tutoriales', 'description' => 'Guías paso a paso para aprender tecnologías del futuro.'],
            ['name' => 'Noticias', 'slug' => 'noticias', 'description' => 'Las últimas novedades del ecosistema tecnológico.'],
            ['name' => 'Opinión', 'slug' => 'opinion', 'description' => 'Perspectivas y análisis de expertos de la industria.'],
            ['name' => 'Análisis', 'slug' => 'analisis', 'description' => 'Análisis profundos sobre tendencias y arquitecturas web.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
