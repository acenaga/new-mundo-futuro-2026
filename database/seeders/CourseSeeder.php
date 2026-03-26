<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::first() ?? User::factory()->create(['name' => 'Alex Vanguard', 'email' => 'alex@mundofuturo.com']);

        $courses = [
            [
                'title' => 'Mastering Next.js 14 & Edge Computing',
                'description' => 'Optimización extrema, Server Components y despliegue global en el borde. Aprende a construir aplicaciones web de alto rendimiento con las últimas características de Next.js.',
                'duration_label' => '42 Horas',
                'badge' => 'BEST SELLER',
                'level' => 'Avanzado',
            ],
            [
                'title' => 'Three.js: La Web Visual',
                'description' => 'Crea experiencias 3D impresionantes en el navegador. Desde la geometría básica hasta shaders avanzados y animaciones fluidas.',
                'duration_label' => '24 Horas',
                'badge' => null,
                'level' => 'Intermedio',
            ],
            [
                'title' => 'Ética Hacker para la Web',
                'description' => 'Aprende a identificar cuellos de botella y fugas de memoria usando Chrome DevTools. Seguridad ofensiva aplicada al desarrollo web.',
                'duration_label' => '30 Horas',
                'badge' => null,
                'level' => 'Avanzado',
            ],
            [
                'title' => 'IA Generativa en el Frontend',
                'description' => 'Integra modelos de lenguaje y generación de imágenes en tus aplicaciones web. Construye interfaces inteligentes con APIs de OpenAI y Anthropic.',
                'duration_label' => '18 Horas',
                'badge' => 'NUEVO',
                'level' => 'Intermedio',
            ],
            [
                'title' => 'Rust para Desarrolladores Web',
                'description' => 'Domina el lenguaje más amado por los desarrolladores. Construye herramientas de línea de comandos, servidores web ultrarrápidos y módulos WebAssembly.',
                'duration_label' => '36 Horas',
                'badge' => null,
                'level' => 'Avanzado',
            ],
            [
                'title' => 'Micro-Frontends con Module Federation',
                'description' => 'Arquitecturas escalables para equipos grandes. Aprende a dividir tu frontend en módulos independientes que se despliegan por separado.',
                'duration_label' => '20 Horas',
                'badge' => null,
                'level' => 'Avanzado',
            ],
        ];

        foreach ($courses as $data) {
            $slug = Str::slug($data['title']);

            Course::firstOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $teacher->id,
                    'title' => $data['title'],
                    'slug' => $slug,
                    'description' => $data['description'],
                    'image_path' => null,
                    'status' => 'published',
                    'is_premium' => false,
                ]
            );
        }
    }
}
