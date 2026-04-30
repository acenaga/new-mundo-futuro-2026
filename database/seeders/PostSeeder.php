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
        $tutorialCategory = Category::where('slug', 'tutoriales')->firstOrFail();
        $noticiaCategory = Category::where('slug', 'noticias')->firstOrFail();
        $opinionCategory = Category::where('slug', 'opinion')->firstOrFail();
        $analisisCategory = Category::where('slug', 'analisis')->firstOrFail();

        // ── Tutorials ────────────────────────────────────────────────────────
        $tutorials = [
            [
                'title' => 'Micro-Interacciones con Framer Motion',
                'excerpt' => 'Lleva tus interfaces de estáticas a dinámicas con técnicas avanzadas de animación reactiva. Aprende a crear transiciones fluidas que mejoran la experiencia de usuario.',
                'tags' => ['Frontend', 'JavaScript'],
                'days_ago' => 1,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Auth de Próxima Gen con Supabase',
                'excerpt' => 'Implementación de flujos de autenticación biométrica y Passionless en tus apps modernas. Olvídate de las contraseñas para siempre.',
                'tags' => ['Backend', 'JavaScript'],
                'days_ago' => 3,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Profiling de Rendimiento Extremo',
                'excerpt' => 'Aprende a identificar cuellos de botella y fugas de memoria usando Chrome DevTools. Técnicas para optimizar al máximo tus aplicaciones.',
                'tags' => ['Frontend', 'JavaScript'],
                'days_ago' => 6,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'WebAssembly desde Cero con Rust',
                'excerpt' => 'Compila módulos Rust a WebAssembly y úsalos directamente en el navegador. La forma más rápida de ejecutar código nativo en la web.',
                'tags' => ['Rust', 'WebGL', 'Frontend'],
                'days_ago' => 10,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Server Components en React 19: Guía Definitiva',
                'excerpt' => 'Los React Server Components cambian por completo el modelo mental del renderizado. Aprende cuándo usarlos, cuándo no, y cómo migrar tu app existente.',
                'tags' => ['Frontend', 'JavaScript'],
                'days_ago' => 14,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Edge Functions: Computación al Límite de la Red',
                'excerpt' => 'Despliega lógica de servidor a centros de datos distribuidos globalmente. Cómo Vercel Edge, Cloudflare Workers y Deno Deploy están redefiniendo el backend.',
                'tags' => ['Backend', 'Arquitectura'],
                'days_ago' => 18,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Animaciones 3D con Three.js y React Three Fiber',
                'excerpt' => 'Crea experiencias 3D interactivas directamente en el navegador. Desde un cubo rotante hasta escenas completas con física y partículas.',
                'tags' => ['WebGL', 'Frontend', 'JavaScript'],
                'days_ago' => 22,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Testing End-to-End con Playwright',
                'excerpt' => 'Playwright superó a Cypress como el estándar del sector. Aprende a escribir tests robustos que corren en Chromium, Firefox y WebKit simultáneamente.',
                'tags' => ['Frontend', 'JavaScript'],
                'days_ago' => 26,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'GraphQL Subscriptions en Tiempo Real',
                'excerpt' => 'Construye feeds en vivo, chats y dashboards reactivos usando GraphQL Subscriptions sobre WebSockets. Implementación completa con Apollo Server.',
                'tags' => ['Backend', 'JavaScript'],
                'days_ago' => 30,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Diseño de APIs REST con Principios HATEOAS',
                'excerpt' => 'Más allá del CRUD básico. Aprende a diseñar APIs autodescriptivas que guíen al cliente a través de los flujos de negocio sin documentación adicional.',
                'tags' => ['Backend', 'Arquitectura'],
                'days_ago' => 35,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Optimización de Imágenes para la Web Moderna',
                'excerpt' => 'AVIF, WebP, lazy loading nativo y el art direction con `<picture>`. Una guía completa para que tus imágenes no destruyan tu Core Web Vitals.',
                'tags' => ['Frontend'],
                'days_ago' => 40,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Introducción a Rust para Desarrolladores JavaScript',
                'excerpt' => 'Rust es el lenguaje que todo dev web debería conocer. Aprende ownership, borrowing y traits desde la perspectiva de alguien que viene de JS.',
                'tags' => ['Rust', 'Backend'],
                'days_ago' => 45,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Construye tu Propio ORM en TypeScript',
                'excerpt' => 'Entender cómo funciona un ORM por dentro te convierte en mejor desarrollador. Construimos un mini-ORM con tipos genéricos, query builder y migraciones.',
                'tags' => ['Backend', 'JavaScript'],
                'days_ago' => 50,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'CSS Container Queries: El Futuro del Diseño Responsivo',
                'excerpt' => 'Olvida los media queries basados en viewport. Las container queries permiten que los componentes respondan a su propio tamaño, no al de la pantalla.',
                'tags' => ['Frontend'],
                'days_ago' => 55,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
            [
                'title' => 'Monorepos con Turborepo: Escala tu Codebase',
                'excerpt' => 'Gestiona múltiples apps y paquetes en un único repositorio sin perder velocidad. Turborepo con caché remota y pipelines paralelas en la práctica.',
                'tags' => ['Frontend', 'Backend', 'Arquitectura'],
                'days_ago' => 60,
                'video_url' => 'https://www.youtube.com/watch?v=Cn8HBj8QAbk',
            ],
        ];

        foreach ($tutorials as $data) {
            $slug = Str::slug($data['title']);
            $post = Post::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $author->id,
                    'category_id' => $tutorialCategory->id,
                    'title' => $data['title'],
                    'slug' => $slug,
                    'excerpt' => $data['excerpt'],
                    'body' => $data['excerpt'] . ' ' . str_repeat('Contenido detallado del tutorial. ', 20),
                    'cover_image_path' => null,
                    'video_url' => $data['video_url'],
                    'status' => PostStatus::Published,
                    'published_at' => now()->subDays($data['days_ago']),
                    'allow_comments' => true,
                ]
            );

            $postTags = collect($data['tags'])->map(fn($t) => $tags[$t]->id)->toArray();
            $post->tags()->sync($postTags);
        }

        // ── Publications ─────────────────────────────────────────────────────
        $publications = [
            [
                'title' => 'El Surgimiento de la Web Cognitiva',
                'excerpt' => '¿Cómo la inteligencia artificial generativa está cambiando fundamentalmente la manera en que consumimos y generamos interfaces dinámicas en tiempo real?',
                'category' => $noticiaCategory,
                'tags' => ['IA', 'Frontend'],
                'days_ago' => 2,
            ],
            [
                'title' => 'Arquitecturas "State-of-the-Art" en 2024',
                'excerpt' => 'Un análisis exhaustivo de por qué las arquitecturas modulares están ganando la batalla frente a los micro-frontends convencionales.',
                'category' => $analisisCategory,
                'tags' => ['Arquitectura', 'Frontend'],
                'days_ago' => 12,
            ],
            [
                'title' => 'IA es el Frontend',
                'excerpt' => 'La inteligencia artificial no solo está cambiando el backend; está redefiniendo cómo construimos y entregamos interfaces de usuario.',
                'category' => $opinionCategory,
                'tags' => ['IA', 'Frontend'],
                'days_ago' => 18,
            ],
            [
                'title' => 'El Ocaso de los CMS Tradicionales',
                'excerpt' => 'WordPress, Drupal y los CMS monolíticos están perdiendo terreno frente a soluciones headless y edge-first. ¿Cuál es el futuro del content management?',
                'category' => $analisisCategory,
                'tags' => ['Arquitectura', 'Backend'],
                'days_ago' => 25,
            ],
            [
                'title' => 'El Modelo de Lenguaje que Escribió su Propio Compilador',
                'excerpt' => 'DeepMind publicó un paper donde un LLM genera, evalúa y optimiza su propio código de bajo nivel. Estamos ante el inicio de la auto-mejora continua.',
                'category' => $noticiaCategory,
                'tags' => ['IA', 'Backend'],
                'days_ago' => 30,
            ],
            [
                'title' => 'Por Qué Abandoné React después de 7 Años',
                'excerpt' => 'No es un rage-quit. Es una decisión meditada sobre complejidad accidental, bundle size y el coste real de mantener una app grande en producción.',
                'category' => $opinionCategory,
                'tags' => ['Frontend', 'JavaScript'],
                'days_ago' => 35,
            ],
            [
                'title' => 'State of JavaScript 2025: Los Números que Importan',
                'excerpt' => 'Analizamos la encuesta más importante del ecosistema JS con datos de 30.000 desarrolladores. Frameworks emergentes, herramientas en declive y sorpresas.',
                'category' => $analisisCategory,
                'tags' => ['JavaScript', 'Frontend'],
                'days_ago' => 40,
            ],
            [
                'title' => 'Vercel vs Cloudflare: La Guerra por el Edge',
                'excerpt' => 'Dos gigantes peleando por ser el runtime de tu backend. Comparamos latencia, precios, DX y límites de ejecución en escenarios reales de producción.',
                'category' => $analisisCategory,
                'tags' => ['Backend', 'Arquitectura'],
                'days_ago' => 45,
            ],
            [
                'title' => 'Rust Entra al Kernel de Linux: Lo que Significa para el Web Dev',
                'excerpt' => 'La incorporación de Rust en el kernel de Linux no es solo una curiosidad técnica. Tiene implicaciones directas en cómo escribiremos software de sistema los próximos años.',
                'category' => $noticiaCategory,
                'tags' => ['Rust', 'Backend'],
                'days_ago' => 50,
            ],
            [
                'title' => 'El Fin del Bundle: Importmaps y el Regreso de los Módulos Nativos',
                'excerpt' => 'Durante años empaquetamos todo porque los navegadores no podían manejar módulos ES eficientemente. Ya no es así. ¿Tiene sentido seguir usando Webpack?',
                'category' => $opinionCategory,
                'tags' => ['Frontend', 'JavaScript'],
                'days_ago' => 55,
            ],
            [
                'title' => 'Cómo Figma Rediseñó su Motor de Renderizado en Rust',
                'excerpt' => 'El equipo de Figma migró partes críticas de su motor de renderizado de C++ a Rust y lo compiló a WebAssembly. Resultados: 3x de mejora en performance.',
                'category' => $noticiaCategory,
                'tags' => ['Rust', 'WebGL', 'Frontend'],
                'days_ago' => 60,
            ],
            [
                'title' => 'El Desarrollador Full Stack ya no Existe',
                'excerpt' => 'La especialización ganó la batalla. Con IA generando el 40% del código, el tiempo que ahorras en un área lo necesitas para profundizar en otra.',
                'category' => $opinionCategory,
                'tags' => ['IA', 'Arquitectura'],
                'days_ago' => 65,
            ],
            [
                'title' => 'Análisis: ¿Qué tan Seguros son los LLMs como Copilots de Código?',
                'excerpt' => 'Estudiamos 500 snippets generados por GitHub Copilot, ChatGPT y Claude. El 23% contenía vulnerabilidades explotables. Metodología, hallazgos y recomendaciones.',
                'category' => $analisisCategory,
                'tags' => ['IA', 'Backend'],
                'days_ago' => 70,
            ],
            [
                'title' => 'WebGPU: La API Gráfica que Cambia Todo',
                'excerpt' => 'WebGPU llegó a Chrome estable y promete llevar las capacidades de la GPU al navegador de forma estandarizada. Machine learning, gráficos 3D y computación paralela en la web.',
                'category' => $noticiaCategory,
                'tags' => ['WebGL', 'Frontend'],
                'days_ago' => 75,
            ],
            [
                'title' => 'Micro-Frontends en Producción: 18 Meses Después',
                'excerpt' => 'Adoptamos micro-frontends en un equipo de 40 devs. Este es el informe honesto: qué funcionó, qué fallamos, qué haríamos diferente y si lo recomendaríamos.',
                'category' => $analisisCategory,
                'tags' => ['Arquitectura', 'Frontend'],
                'days_ago' => 80,
            ],
            [
                'title' => 'SQLite en el Servidor: El Renacimiento de una Base de Datos',
                'excerpt' => 'Turso, Cloudflare D1 y Litestream están llevando SQLite a producción distribuida. Por qué esta base de datos de 30 años es la respuesta a muchos problemas modernos.',
                'category' => $analisisCategory,
                'tags' => ['Backend', 'Arquitectura'],
                'days_ago' => 85,
            ],
            [
                'title' => 'La Deuda Técnica de la IA Generativa',
                'excerpt' => 'Código generado por IA sin revisión es deuda técnica disfrazada de productividad. Métricas reales de equipos que llevan 1 año usando Copilot intensivamente.',
                'category' => $opinionCategory,
                'tags' => ['IA', 'Arquitectura'],
                'days_ago' => 90,
            ],
            [
                'title' => 'Bun 2.0: ¿Es Realmente el Killer de Node.js?',
                'excerpt' => 'Probamos Bun 2.0 en un proyecto real de Next.js con 200K líneas de código. Benchmarks, compatibilidad y todo lo que necesitas saber antes de migrar.',
                'category' => $analisisCategory,
                'tags' => ['Backend', 'JavaScript'],
                'days_ago' => 95,
            ],
            [
                'title' => 'Accesibilidad Web en 2025: El Estado Actual',
                'excerpt' => 'Solo el 3% de los sitios web son accesibles según los estándares WCAG 2.2. Analizamos las barreras técnicas, legales y culturales que impiden el avance.',
                'category' => $noticiaCategory,
                'tags' => ['Frontend'],
                'days_ago' => 100,
            ],
            [
                'title' => 'Cómo Stripe Procesa 1 Millón de Transacciones por Segundo',
                'excerpt' => 'Deep dive en la arquitectura de Stripe: cómo distribuyen carga, gestionan consistencia eventual y mantienen 99.9999% de uptime con un sistema globalmente distribuido.',
                'category' => $analisisCategory,
                'tags' => ['Backend', 'Arquitectura'],
                'days_ago' => 108,
            ],
            [
                'title' => 'El Problema con los Design Systems a Escala',
                'excerpt' => 'Los design systems son maravillosos en papel. En la realidad, se convierten en cuellos de botella, generan deuda y crean conflictos de propiedad. La solución no es técnica.',
                'category' => $opinionCategory,
                'tags' => ['Frontend', 'Arquitectura'],
                'days_ago' => 115,
            ],
            [
                'title' => 'TypeScript 6.0: Las Funcionalidades que Cambiarán tu Código',
                'excerpt' => 'Repasamos las propuestas más esperadas del roadmap de TypeScript 6: types-as-values, pattern matching nativo y mejoras en el sistema de inferencia.',
                'category' => $noticiaCategory,
                'tags' => ['JavaScript', 'Frontend'],
                'days_ago' => 120,
            ],
            [
                'title' => 'Privacidad por Diseño: Más Allá del GDPR',
                'excerpt' => 'Construir productos que respetan la privacidad del usuario no debería ser una obligación legal, sino un principio de ingeniería. Patrones concretos para implementarlo.',
                'category' => $opinionCategory,
                'tags' => ['Backend', 'Arquitectura'],
                'days_ago' => 125,
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
                    'body' => $data['excerpt'] . ' ' . str_repeat('Contenido del artículo de publicación. ', 20),
                    'cover_image_path' => null,
                    'status' => PostStatus::Published,
                    'published_at' => now()->subDays($data['days_ago']),
                    'allow_comments' => true,
                ]
            );

            $postTags = collect($data['tags'])->map(fn($t) => $tags[$t]->id)->toArray();
            $post->tags()->sync($postTags);
        }
    }
}
