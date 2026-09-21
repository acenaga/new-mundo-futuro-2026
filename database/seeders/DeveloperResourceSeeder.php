<?php

namespace Database\Seeders;

use App\Enums\DeveloperResourcePricing;
use App\Enums\DeveloperResourceStatus;
use App\Models\DeveloperResource;
use App\Models\DeveloperResourceCategory;
use App\Models\DeveloperResourceTechnology;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeveloperResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::first() ?? User::factory()->create([
            'name' => 'Equipo Mundo Futuro',
            'email' => 'equipo@mundofuturo.test',
        ]);

        $categories = DeveloperResourceCategory::query()->get()->keyBy('slug');
        $technologies = collect([
            'AI', 'API', 'CSS', 'Docker', 'Git', 'JavaScript', 'Laravel', 'PHP', 'Playwright', 'React', 'SQL', 'TypeScript',
        ])->mapWithKeys(fn (string $name): array => [
            $name => DeveloperResourceTechnology::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            ),
        ]);

        foreach ([
            ['name' => 'Laravel Cloud', 'category' => 'hosting-deploy', 'external_url' => 'https://cloud.laravel.com', 'summary' => 'Plataforma gestionada para desplegar y escalar aplicaciones Laravel sin encargarte de la infraestructura base.', 'why_use_it' => 'Reduce el trabajo operativo si tu equipo ya trabaja con Laravel y busca un flujo de despliegue integrado.', 'when_not_to_use_it' => 'No es la mejor elección si necesitas una infraestructura muy personalizada o no trabajas con Laravel.', 'pricing' => DeveloperResourcePricing::Freemium, 'free_tier_details' => 'Ofrece una experiencia inicial para evaluar el flujo de despliegue antes de escalar recursos y servicios.', 'no_card_required' => true, 'is_featured' => true, 'technologies' => ['Laravel', 'PHP']],
            ['name' => 'Cloudflare', 'category' => 'hosting-deploy', 'external_url' => 'https://www.cloudflare.com', 'summary' => 'Red global con CDN, DNS, seguridad y herramientas de edge computing para proyectos web.', 'why_use_it' => 'Es útil para mejorar rendimiento y proteger servicios públicos con una configuración inicial rápida.', 'when_not_to_use_it' => 'Puede añadir complejidad si tu proyecto no necesita una capa de red adicional.', 'pricing' => DeveloperResourcePricing::Freemium, 'free_tier_details' => 'El plan gratuito cubre funciones esenciales de DNS, CDN y protección para proyectos pequeños.', 'no_card_required' => true, 'is_featured' => true, 'technologies' => ['API', 'JavaScript']],
            ['name' => 'Playwright', 'category' => 'testing-observabilidad', 'external_url' => 'https://playwright.dev', 'summary' => 'Framework de automatización para pruebas end-to-end en Chromium, Firefox y WebKit.', 'why_use_it' => 'Permite validar flujos críticos de usuario en navegadores reales desde una única suite de pruebas.', 'when_not_to_use_it' => 'No reemplaza pruebas unitarias rápidas ni es ideal para comprobar lógica aislada.', 'pricing' => DeveloperResourcePricing::Free, 'free_tier_details' => 'Es software de código abierto y puede usarse sin coste de licencia.', 'no_card_required' => true, 'is_featured' => true, 'technologies' => ['JavaScript', 'Playwright', 'TypeScript']],
            ['name' => 'Sentry', 'category' => 'testing-observabilidad', 'external_url' => 'https://sentry.io', 'summary' => 'Monitoreo de errores y rendimiento para detectar fallos antes de que afecten a más usuarios.', 'why_use_it' => 'Centraliza excepciones, trazas y contexto de usuario para acelerar el diagnóstico de incidentes.', 'when_not_to_use_it' => 'Su valor disminuye en prototipos efímeros sin usuarios ni operación continua.', 'pricing' => DeveloperResourcePricing::Freemium, 'free_tier_details' => 'Incluye una cuota inicial de eventos para proyectos pequeños y entornos de prueba.', 'no_card_required' => true, 'is_featured' => false, 'technologies' => ['JavaScript', 'PHP']],
            ['name' => 'Postman', 'category' => 'apis-automatizacion', 'external_url' => 'https://www.postman.com', 'summary' => 'Espacio de trabajo para diseñar, probar y documentar APIs con colecciones compartidas.', 'why_use_it' => 'Facilita explorar endpoints y alinear contratos entre backend, frontend y QA.', 'when_not_to_use_it' => 'Puede resultar excesivo si solo necesitas ejecutar una petición HTTP puntual desde la terminal.', 'pricing' => DeveloperResourcePricing::Freemium, 'free_tier_details' => 'El plan inicial permite crear colecciones y colaborar en proyectos personales o pequeños.', 'no_card_required' => true, 'is_featured' => false, 'technologies' => ['API']],
            ['name' => 'Figma', 'category' => 'diseno-productividad', 'external_url' => 'https://www.figma.com', 'summary' => 'Herramienta colaborativa para diseñar interfaces, prototipos y sistemas de diseño en el navegador.', 'why_use_it' => 'Acerca diseño y desarrollo al trabajar sobre el mismo archivo y facilitar la inspección de componentes.', 'when_not_to_use_it' => 'No sustituye un editor gráfico especializado para ilustración compleja o edición fotográfica avanzada.', 'pricing' => DeveloperResourcePricing::Freemium, 'free_tier_details' => 'El plan Starter permite crear archivos de diseño y colaborar en proyectos personales.', 'no_card_required' => true, 'is_featured' => false, 'technologies' => ['CSS']],
            ['name' => 'GitHub Actions', 'category' => 'herramientas-ci', 'external_url' => 'https://github.com/features/actions', 'summary' => 'Automatización de integración continua y despliegues directamente desde los repositorios de GitHub.', 'why_use_it' => 'Mantiene pruebas, revisiones y despliegues cerca del código y de las pull requests.', 'when_not_to_use_it' => 'No es conveniente si tu organización ya tiene una plataforma de CI centralizada y consolidada.', 'pricing' => DeveloperResourcePricing::Freemium, 'free_tier_details' => 'Incluye cuotas de uso según el tipo de repositorio y sistema operativo de los runners.', 'no_card_required' => true, 'is_featured' => false, 'technologies' => ['Git']],
            ['name' => 'Neon', 'category' => 'backend-datos', 'external_url' => 'https://neon.com', 'summary' => 'Postgres serverless con ramas de base de datos para experimentar y aislar cambios por entorno.', 'why_use_it' => 'Resulta práctico para proyectos web que necesitan Postgres administrado con flujos de ramas rápidos.', 'when_not_to_use_it' => 'Evalúa otra opción si requieres control total de red, extensiones poco comunes o una topología específica.', 'pricing' => DeveloperResourcePricing::Freemium, 'free_tier_details' => 'El plan gratuito está orientado a proyectos de aprendizaje, prototipos y cargas iniciales.', 'no_card_required' => true, 'is_featured' => false, 'technologies' => ['SQL']],
            ['name' => 'Supabase', 'category' => 'backend-datos', 'external_url' => 'https://supabase.com', 'summary' => 'Backend gestionado con Postgres, autenticación, almacenamiento y APIs generadas automáticamente.', 'why_use_it' => 'Acelera la creación de productos cuando necesitas una base de datos y servicios de backend listos para usar.', 'when_not_to_use_it' => 'No es ideal si tu dominio exige una arquitectura de backend muy específica desde el primer día.', 'pricing' => DeveloperResourcePricing::Freemium, 'free_tier_details' => 'Incluye un proyecto inicial con cuotas adecuadas para prototipos y aplicaciones pequeñas.', 'no_card_required' => true, 'is_featured' => false, 'technologies' => ['API', 'SQL']],
            ['name' => 'Docker', 'category' => 'herramientas-ci', 'external_url' => 'https://www.docker.com', 'summary' => 'Plataforma para empaquetar aplicaciones y sus dependencias en contenedores reproducibles.', 'why_use_it' => 'Ayuda a reducir diferencias entre los entornos locales, de pruebas y de producción.', 'when_not_to_use_it' => 'Puede añadir complejidad a proyectos pequeños que no tienen dependencias de infraestructura relevantes.', 'pricing' => DeveloperResourcePricing::Freemium, 'free_tier_details' => 'La herramienta base permite trabajar con contenedores locales sin coste para uso personal.', 'no_card_required' => true, 'is_featured' => false, 'technologies' => ['Docker']],
            ['name' => 'OpenAI API', 'category' => 'ia-ml', 'external_url' => 'https://platform.openai.com', 'summary' => 'Plataforma para integrar modelos de IA en productos mediante APIs de texto, visión y audio.', 'why_use_it' => 'Permite validar funciones de IA generativa sin entrenar ni operar modelos propios.', 'when_not_to_use_it' => 'No es adecuada si tu caso exige ejecución completamente offline o controles de datos incompatibles con un proveedor externo.', 'pricing' => DeveloperResourcePricing::Freemium, 'free_tier_details' => 'El acceso y los créditos promocionales pueden variar; revisa los límites vigentes antes de integrar.', 'no_card_required' => false, 'is_featured' => false, 'technologies' => ['AI', 'API']],
            ['name' => 'React', 'category' => 'frontend-ui', 'external_url' => 'https://react.dev', 'summary' => 'Biblioteca de interfaz para construir componentes declarativos y aplicaciones web interactivas.', 'why_use_it' => 'Su ecosistema y modelo de componentes son útiles para interfaces complejas que evolucionan con frecuencia.', 'when_not_to_use_it' => 'Una página mayormente estática puede requerir menos complejidad y menos JavaScript.', 'pricing' => DeveloperResourcePricing::Free, 'free_tier_details' => 'Es software de código abierto y puede utilizarse sin coste de licencia.', 'no_card_required' => true, 'is_featured' => false, 'technologies' => ['JavaScript', 'React', 'TypeScript']],
        ] as $data) {
            $category = $categories->get($data['category'])
                ?? throw new \LogicException("La categoría [{$data['category']}] no existe.");
            $slug = Str::slug($data['name']);

            $resource = DeveloperResource::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $author->id,
                    'developer_resource_category_id' => $category->id,
                    'name' => $data['name'],
                    'external_url' => $data['external_url'],
                    'summary' => $data['summary'],
                    'why_use_it' => $data['why_use_it'],
                    'when_not_to_use_it' => $data['when_not_to_use_it'],
                    'pricing' => $data['pricing'],
                    'free_tier_details' => $data['free_tier_details'],
                    'no_card_required' => $data['no_card_required'],
                    'is_featured' => $data['is_featured'],
                    'status' => DeveloperResourceStatus::Published,
                    'last_verified_at' => now(),
                    'published_at' => now(),
                ],
            );

            $resource->technologies()->sync(
                collect($data['technologies'])
                    ->map(fn (string $technology): int => $technologies->get($technology)->id)
                    ->all(),
            );
        }
    }
}
