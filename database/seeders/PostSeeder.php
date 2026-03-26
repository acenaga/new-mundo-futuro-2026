<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first() ?? User::factory()->create(['name' => 'Alex Vanguard', 'email' => 'alex@mundofuturo.com']);

        // ── Tags ─────────────────────────────────────────────────────────────
        $tagNames = ['JavaScript', 'WebGL', 'IA', 'Backend', 'Frontend', 'Arquitectura', 'Rust', 'Next.js', 'Web3'];
        $tags = collect($tagNames)->mapWithKeys(function (string $name) {
            return [$name => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])];
        });

        // ── Categories ───────────────────────────────────────────────────────
        $tutorialCategory = Category::where('slug', 'tutorials')->firstOrFail();
        $noticiaCategory = Category::where('slug', 'noticias')->firstOrFail();
        $opinionCategory = Category::where('slug', 'opinion')->firstOrFail();
        $analisisCategory = Category::where('slug', 'analisis')->firstOrFail();

        // ── Tutorials ────────────────────────────────────────────────────────
        $tutorials = [
            [
                'title' => 'Micro-Interacciones con Framer Motion',
                'excerpt' => 'Lleva tus interfaces de estáticas a dinámicas con técnicas avanzadas de animación reactiva. Aprende a crear transiciones fluidas que mejoran la experiencia de usuario.',
                'read_time' => 15,
                'tags' => ['Frontend', 'JavaScript'],
            ],
            [
                'title' => 'Auth de Próxima Gen con Supabase',
                'excerpt' => 'Implementación de flujos de autenticación biométrica y Passionless en tus apps modernas. Olvídate de las contraseñas para siempre.',
                'read_time' => 22,
                'tags' => ['Backend', 'JavaScript'],
            ],
            [
                'title' => 'Profiling de Rendimiento Extremo',
                'excerpt' => 'Aprende a identificar cuellos de botella y fugas de memoria usando Chrome DevTools. Técnicas para optimizar al máximo tus aplicaciones.',
                'read_time' => 35,
                'tags' => ['Frontend', 'JavaScript'],
            ],
            [
                'title' => 'WebAssembly desde Cero con Rust',
                'excerpt' => 'Compila módulos Rust a WebAssembly y úsalos directamente en el navegador. La forma más rápida de ejecutar código nativo en la web.',
                'read_time' => 45,
                'tags' => ['Rust', 'WebGL', 'Frontend'],
            ],
        ];

        foreach ($tutorials as $data) {
            $slug = Str::slug($data['title']);
            $post = Post::firstOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $author->id,
                    'category_id' => $tutorialCategory->id,
                    'title' => $data['title'],
                    'slug' => $slug,
                    'excerpt' => $data['excerpt'],
                    'body' => $data['excerpt'].' '.str_repeat('Contenido detallado del tutorial. ', 20),
                    'cover_image_path' => null,
                    'status' => PostStatus::Published,
                    'published_at' => now()->subDays(rand(1, 30)),
                    'allow_comments' => true,
                ]
            );

            $postTags = collect($data['tags'])->map(fn ($t) => $tags[$t]->id)->toArray();
            $post->tags()->sync($postTags);
        }

        // ── Publications ─────────────────────────────────────────────────────
        $publications = [
            [
                'title' => 'El Surgimiento de la Web Cognitiva',
                'excerpt' => '¿Cómo la inteligencia artificial generativa está cambiando fundamentalmente la manera en que consumimos y generamos interfaces dinámicas en tiempo real?',
                'category' => $noticiaCategory,
                'read_time' => 8,
                'tags' => ['IA', 'Frontend'],
                'days_ago' => 2,
            ],
            [
                'title' => 'Arquitecturas "State-of-the-Art" en 2024',
                'excerpt' => 'Un análisis exhaustivo de por qué las arquitecturas modulares están ganando la batalla frente a los micro-frontends convencionales.',
                'category' => $analisisCategory,
                'read_time' => 14,
                'tags' => ['Arquitectura', 'Frontend'],
                'days_ago' => 12,
            ],
            [
                'title' => 'IA es el Frontend',
                'excerpt' => 'La inteligencia artificial no solo está cambiando el backend; está redefiniendo cómo construimos y entregamos interfaces de usuario.',
                'category' => $opinionCategory,
                'read_time' => 6,
                'tags' => ['IA', 'Frontend'],
                'days_ago' => 18,
            ],
            [
                'title' => 'El Ocaso de los CMS Tradicionales',
                'excerpt' => 'WordPress, Drupal y los CMS monolíticos están perdiendo terreno frente a soluciones headless y edge-first. ¿Cuál es el futuro del content management?',
                'category' => $analisisCategory,
                'read_time' => 10,
                'tags' => ['Arquitectura', 'Backend'],
                'days_ago' => 25,
            ],
        ];

        foreach ($publications as $data) {
            $slug = Str::slug($data['title']);
            $post = Post::firstOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $author->id,
                    'category_id' => $data['category']->id,
                    'title' => $data['title'],
                    'slug' => $slug,
                    'excerpt' => $data['excerpt'],
                    'body' => $data['excerpt'].' '.str_repeat('Contenido del artículo de publicación. ', 20),
                    'cover_image_path' => null,
                    'status' => PostStatus::Published,
                    'published_at' => now()->subDays($data['days_ago']),
                    'allow_comments' => true,
                ]
            );

            $postTags = collect($data['tags'])->map(fn ($t) => $tags[$t]->id)->toArray();
            $post->tags()->sync($postTags);
        }
    }
}
