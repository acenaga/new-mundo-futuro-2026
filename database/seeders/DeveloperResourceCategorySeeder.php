<?php

namespace Database\Seeders;

use App\Models\DeveloperResourceCategory;
use Illuminate\Database\Seeder;

class DeveloperResourceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'IA y ML', 'slug' => 'ia-ml', 'icon' => 'sparkles'],
            ['name' => 'Frontend y UI', 'slug' => 'frontend-ui', 'icon' => 'paint-brush'],
            ['name' => 'Backend y Datos', 'slug' => 'backend-datos', 'icon' => 'circle-stack'],
            ['name' => 'Hosting y Deploy', 'slug' => 'hosting-deploy', 'icon' => 'cloud'],
            ['name' => 'Herramientas y CI', 'slug' => 'herramientas-ci', 'icon' => 'wrench-screwdriver'],
            ['name' => 'APIs y Automatización', 'slug' => 'apis-automatizacion', 'icon' => 'arrows-right-left'],
            ['name' => 'Auth y Seguridad', 'slug' => 'auth-seguridad', 'icon' => 'shield-check'],
            ['name' => 'Testing y Observabilidad', 'slug' => 'testing-observabilidad', 'icon' => 'chart-bar'],
            ['name' => 'Diseño y Productividad', 'slug' => 'diseno-productividad', 'icon' => 'swatch'],
        ] as $index => $category) {
            DeveloperResourceCategory::updateOrCreate(
                ['slug' => $category['slug']],
                [...$category, 'sort_order' => $index + 1],
            );
        }
    }
}
