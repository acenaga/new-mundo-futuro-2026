# Mundo Futuro

Plataforma educativa de próxima generación enfocada en desarrollo web avanzado. Construida con Laravel 13, Livewire 4, Flux UI y Tailwind CSS 4.

## Stack

| Tecnología | Versión |
|---|---|
| PHP | 8.4 |
| Laravel | 13 |
| Livewire | 4 |
| Flux UI | 2 |
| Filament | 5 |
| Tailwind CSS | 4 |
| Vite | latest |

## Requisitos

- PHP 8.4+
- Node.js 20+
- Laravel Herd (o servidor local compatible)
- Base de datos compatible (MySQL / SQLite)

## Instalación

```bash
git clone <repo>
cd new-mundo-futuro

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan db:seed

npm run build
```

## Desarrollo

```bash
composer run dev   # Inicia Laravel + Vite en paralelo
```

O por separado:

```bash
php artisan serve
npm run dev
```

## Bootstrap de accesos

La creación de roles base y del usuario `super_admin` ya no depende de `db:seed` para crear usuarios administrativos. Ahora el proyecto tiene dos comandos Artisan dedicados.

### Roles base

Este comando crea de forma idempotente los roles base de la aplicación:

```bash
php artisan access:create-roles
```

Roles creados:

- `super_admin`
- `admin`
- `tutor`
- `editor`
- `student`

La misma lista de roles vive en `database/seeders/RoleSeeder.php`, por lo que el seeder y el comando comparten la misma fuente de verdad.

### Super admin

Este comando crea o actualiza un usuario con rol `super_admin`, verifica su correo si aún no estaba verificado y permite definir la contraseña de forma interactiva o por opciones.

Modo interactivo:

```bash
php artisan access:create-super-admin
```

Modo no interactivo:

```bash
php artisan access:create-super-admin --name="Carlos Ferrer" --email="admin@example.com" --password="tu-password-segura"
```

Si no envías `--password`, el comando pedirá la contraseña dos veces y fallará si la confirmación no coincide.

### Recomendación para staging y producción

En despliegues, la recomendación es:

```bash
php artisan access:create-roles
```

Y luego, solo cuando haga falta crear o actualizar el usuario administrador:

```bash
php artisan access:create-super-admin
```

Esto evita dejar credenciales administrativas hardcodeadas en seeders y mantiene el bootstrap de acceso como una operación explícita y segura.

---

## Arquitectura del sitio público

### Sistema de temas (dark/light)

El sitio usa un sistema de temas dual:

- **CSS**: clase `.dark` en `<html>` habilita el variant `dark:` de Tailwind y los selectores `html:not(.dark)` en `app.css`.
- **Alpine.js**: `themeManager()` en `public.blade.php` expone `isDark` para bindings inline `:class="isDark ? ... : ..."`.
- **Preferencia persistida**: `localStorage` bajo la clave `flux_appearance` (compatible con Flux UI).
- **Anti-FOUC**: script bloqueante en `<head>` que aplica `.dark` sincrónicamente antes de que Alpine hidrate, evitando destellos en usuarios con preferencia `light`.

### Logo

El logo tiene dos versiones SVG:

| Archivo | Uso |
|---|---|
| `public/assets/img/logo.svg` | Modo claro (paths `#110090` azul marino) |
| `public/assets/img/logo-dark.svg` | Modo oscuro (paths `#e2e2f0` blanco suave) |

Los `<img>` del logo usan `:src` dinámico con Alpine.js para alternar entre versiones.

### Layout público

```
resources/views/
├── components/
│   ├── layouts/
│   │   └── public.blade.php       # Layout base (html, head, navbar, footer)
│   └── public/
│       ├── navbar.blade.php       # Navbar sticky con toggle de tema
│       └── footer.blade.php       # Footer con links y redes
├── partials/
│   └── head.blade.php             # Meta tags SEO, OG, Twitter Cards, fuentes, Vite
├── welcome.blade.php              # Home page
├── sitemap.blade.php              # Plantilla XML del sitemap dinámico
├── publicaciones/
│   ├── index.blade.php            # Listado de publicaciones
│   └── show.blade.php             # Detalle de publicación (con JSON-LD)
└── tutoriales/
    ├── index.blade.php            # Listado de tutoriales
    └── show.blade.php             # Detalle de tutorial (con JSON-LD)
```

---

## SEO dinámico

El sistema de SEO está construido sobre `partials/head.blade.php` y el layout `public.blade.php`. Todas las páginas del sitio generan automáticamente `<title>`, meta description, etiquetas Open Graph, Twitter Cards y URL canónica.

### Cómo funciona

**1. El layout declara las props SEO**

`public.blade.php` acepta cinco props opcionales:

```blade
@props(['title' => null, 'description' => null, 'ogImage' => null, 'ogType' => 'website', 'canonical' => null])
```

Cada página pasa sus propios valores al instanciar el layout:

```blade
<x-layouts.public
    :title="$post->title . ' — ' . config('app.name')"
    :description="$post->excerpt"
    :ogImage="$post->cover_image_path ? url(Storage::url($post->cover_image_path)) : null"
    ogType="article"
    :canonical="route('tutoriales.show', $post)"
>
```

**2. El partial `head.blade.php` resuelve los valores con fallbacks**

```php
$seoTitle       = $title       ?? config('app.name');
$seoDescription = strip_tags($description ?? 'Descripción por defecto...');
$seoCanonical   = $canonical   ?? url()->current();
$seoOgType      = $ogType      ?? 'website';
$seoOgImage     = $ogImage     ?? asset('apple-touch-icon.png');
$seoLocale      = str_replace('_', '-', app()->getLocale());
```

Si una página no pasa ningún valor, el head usa fallbacks inteligentes: la URL canónica es `url()->current()`, la imagen es el `apple-touch-icon.png` del proyecto, y el tipo OG es `website`.

**3. Tags generados por cada tipo de página**

| Tag | Home | Index | Show (artículos) |
|---|---|---|---|
| `<title>` | ✓ personalizado | ✓ personalizado | ✓ con nombre de la publicación |
| `<meta description>` | ✓ | ✓ | ✓ desde `excerpt` |
| `<link rel="canonical">` | ✓ | ✓ | ✓ con slug de la URL |
| `og:type` | `website` | `website` | `article` |
| `og:title` / `og:description` | ✓ | ✓ | ✓ |
| `og:image` | logo del proyecto | logo del proyecto | ✓ desde `cover_image_path` |
| `twitter:card` | `summary_large_image` | `summary_large_image` | `summary_large_image` |
| JSON-LD (`Article` / `TechArticle`) | — | — | ✓ inyectado en `<head>` |

**4. JSON-LD en páginas de detalle**

Las páginas `show` inyectan datos estructurados en el `<head>` usando `@push('head')`:

```blade
@push('head')
<script type="application/ld+json">
    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'author' => ['@type' => 'Person', 'name' => $post->author?->name ?? config('app.name')],
            'publisher' => ['@type' => 'Organization', 'name' => config('app.name'), 'url' => url('/')],
            'url' => route('publicaciones.show', $post),
        ];

        if ($post->cover_image_path) {
            $jsonLd['image'] = url(Storage::url($post->cover_image_path));
        }
    @endphp
    {!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
```

El layout tiene `@stack('head')` justo antes de `</head>`, por lo que este bloque siempre se coloca en la ubicación correcta.

**5. Sitemap dinámico (`/sitemap.xml`)**

`SitemapController` consulta todos los `Post` publicados, los separa en tutoriales y publicaciones, y renderiza una vista XML:

```php
$posts     = Post::where('status', PostStatus::Published)->with('category')->get();
$tutorials = $posts->filter(fn ($p) => $p->category?->slug === 'tutoriales');
$publicaciones = $posts->filter(fn ($p) => $p->category?->slug !== 'tutoriales');

return response()->view('sitemap', compact('tutorials', 'publicaciones'))
    ->header('Content-Type', 'application/xml');
```

El sitemap se actualiza en tiempo real con cada nueva publicación, sin necesidad de regeneración manual.

### Archivos implicados en el SEO

| Archivo | Rol |
|---|---|
| `resources/views/partials/head.blade.php` | Genera todos los meta tags con fallbacks |
| `resources/views/components/layouts/public.blade.php` | Declara `@props` SEO y `@stack('head')` |
| `resources/views/welcome.blade.php` | Pasa title y description de la home |
| `resources/views/publicaciones/index.blade.php` | Pasa title y description del índice |
| `resources/views/publicaciones/show.blade.php` | Pasa SEO completo + JSON-LD `Article` |
| `resources/views/tutoriales/index.blade.php` | Pasa title y description del índice |
| `resources/views/tutoriales/show.blade.php` | Pasa SEO completo + JSON-LD `TechArticle` |
| `resources/views/sitemap.blade.php` | Plantilla XML del sitemap |
| `app/Http/Controllers/SitemapController.php` | Genera el sitemap dinámico |
| `public/robots.txt` | Directivas para crawlers |

### Configuración en producción

Antes de desplegar, asegúrate de que `APP_URL` en `.env` apunte al dominio real:

```env
APP_URL=https://mundomuturo.com
```

El sitemap y las URLs canónicas se generan a partir de esta variable. Añade también la línea `Sitemap:` al `public/robots.txt`:

```
Sitemap: https://mundofuturo.com/sitemap.xml
```

---

## Rutas públicas

| Método | URI | Nombre | Descripción |
|---|---|---|---|
| GET | `/` | `home` | Página de inicio |
| GET | `/publicaciones` | `publicaciones` | Todas las publicaciones (excepto tutoriales) |
| GET | `/publicaciones/{slug}` | `publicaciones.show` | Detalle de una publicación |
| GET | `/tutoriales` | `tutoriales` | Solo tutoriales |
| GET | `/tutoriales/{slug}` | `tutoriales.show` | Detalle de un tutorial |
| GET | `/sitemap.xml` | `sitemap` | Sitemap XML dinámico |

### Parámetros de filtro

**`/publicaciones`**
- `?categoria=<slug>` — filtra por categoría
- `?tag=<slug>` — filtra por etiqueta
- Ambos son combinables: `?categoria=noticias&tag=ia`

**`/tutoriales`**
- `?tag=<slug>` — filtra por etiqueta

---

## Modelos principales

### `Post`

| Campo | Tipo | Descripción |
|---|---|---|
| `user_id` | FK | Autor |
| `category_id` | FK | Categoría |
| `title` | string | Título |
| `slug` | string unique | URL amigable |
| `excerpt` | text | Resumen |
| `body` | text | Contenido completo |
| `cover_image_path` | string nullable | Ruta de imagen de portada |
| `status` | `PostStatus` enum | `draft` / `published` |
| `published_at` | datetime | Fecha de publicación |

Relaciones: `author` (User), `category` (Category), `tags` (BelongsToMany), `comments` (MorphMany).

### `Category`

Slugs reservados del sistema:

| Slug | Descripción |
|---|---|
| `tutoriales` | Tutoriales (aparece en `/tutoriales`, no en `/publicaciones`) |
| `noticias` | Noticias del ecosistema |
| `opinion` | Artículos de opinión |
| `analisis` | Análisis técnicos |

---

## RichEditor, imágenes y YouTube

El proyecto integra `spatie/laravel-medialibrary` y el plugin oficial de Filament para soportar adjuntos dentro del RichEditor en `Post::body` y `Course::description`.

### Dependencias

| Paquete | Uso |
|---|---|
| `spatie/laravel-medialibrary` | Tabla `media` y gestión de archivos adjuntos |
| `filament/spatie-laravel-media-library-plugin` | Provider de adjuntos para `RichEditor` |

### Integración en modelos

`Post` y `Course` implementan `HasRichContent` + `HasMedia` y registran sus atributos de contenido rico con provider de Spatie.

| Modelo | Atributo RichEditor | Colección de media |
|---|---|---|
| `Post` | `body` | `post-body-attachments` |
| `Course` | `description` | `course-description-attachments` |

### Toolbar de YouTube en Filament

Ambos editores (`PostForm` y `CourseForm`) habilitan tres herramientas:

- `youtubeEmbed` — inserta un video desde URL/ID.
- `youtubeReplace` — actualiza un video existente por otro.
- `youtubeRemove` — elimina un video del contenido.

El plugin vive en:

- `app/Filament/RichEditor/Plugins/YouTubeEmbedRichContentPlugin.php`

### Flujo de render

En edición, el plugin inserta:

1. Un bloque de preview visual (`figure.nmf-youtube-preview`) para UX del editor.
2. Un token persistente: `[[youtube:VIDEO_ID]]`.

En frontend, se renderiza con:

- `App\Support\RichContent\RichContentOutput::render(...)`

Ese renderer:

1. Elimina el bloque de preview (solo editor).
2. Convierte el token al iframe final de YouTube.

Vistas públicas que usan este flujo:

- `resources/views/publicaciones/show.blade.php`
- `resources/views/tutoriales/show.blade.php`

### Seguridad

- Regla `OnlyYouTubeEmbeds` aplicada a ambos RichEditor.
- Solo se aceptan embeds de dominios YouTube permitidos.
- Se mantiene `preventFileAttachmentPathTampering()` en los campos.

### Mantenimiento de adjuntos

Cuando un editor sube una imagen al RichEditor pero cierra el navegador sin guardar, el archivo queda registrado en la tabla `media` sin referencia en el contenido. Para limpiarlos:

```bash
# Inspeccionar sin borrar
php artisan media:cleanup-orphans --dry-run

# Eliminar huérfanos
php artisan media:cleanup-orphans
```

El comando también se ejecuta automáticamente cada semana vía el scheduler de Laravel. Para activar el scheduler en producción:

```bash
* * * * * php /ruta/al/proyecto/artisan schedule:run >> /dev/null 2>&1
```

> El comando `media-library:clean` de Spatie (incluido en el paquete) cubre el caso de adjuntos cuyos modelos fueron eliminados. Ambos comandos son complementarios.

### Tests relacionados

- `tests/Feature/Feature/SeoMetadataTest.php`
- `tests/Feature/Filament/PostResourceTest.php`
- `tests/Feature/CleanupOrphanedMediaCommandTest.php`
- `tests/Unit/Support/YouTubeEmbedTest.php`

### `Tag`

24 etiquetas predefinidas agrupadas en:
- **Áreas técnicas**: Frontend, Backend, Arquitectura, DevOps, Seguridad, Performance
- **Tecnologías emergentes**: IA, Web3, WebAssembly, WebGL, Edge Computing
- **Lenguajes**: JavaScript, TypeScript, Rust, Python, Go
- **Frameworks/Tools**: Next.js, React, Vue, Docker, Node.js
- **Editorial**: Tendencias, Open Source, Industria

---

## Seeders

```bash
php artisan db:seed                        # Todos los seeders
php artisan db:seed --class=TagSeeder      # Solo tags
php artisan db:seed --class=PostSeeder     # Posts y tutoriales de ejemplo
```

Orden de ejecución en `DatabaseSeeder`:

1. `RoleSeeder`
2. `CategorySeeder`
3. `TagSeeder`
4. `CourseSeeder`
5. `PostSeeder`

---

## CSS y estilos

El archivo principal es `resources/css/app.css` (Tailwind CSS 4).

### Paleta de colores ("Galactic Editorial")

| Variable | Valor | Uso |
|---|---|---|
| `--color-surface` | `#12121d` | Fondo base dark |
| `--color-primary` | `#110090` | Azul profundo (modo claro) |
| `--color-tertiary` | `#f4bf27` | Amarillo solar (CTAs) |
| `--color-on-surface` | `#e2e2f0` | Texto principal dark |
| `--color-on-surface-variant` | `#9999b3` | Texto secundario dark |
| `--color-secondary` | `#4c2e84` | Púrpura (acento tutoriales) |

### Clases utilitarias propias

| Clase | Descripción |
|---|---|
| `.hero-section` | Gradiente de fondo del hero (dark/light via `.dark`) |
| `.public-body` | Fondo y color de texto del body público |
| `.clip-hex-corner` | Clip-path para el corte en esquina de cards destacadas |
| `.font-display` | Fuente Space Grotesk |
| `.font-body` | Fuente Inter |

---

## Inteligencia Artificial

El proyecto usa el paquete oficial `laravel/ai` (v0) para integraciones con modelos de lenguaje.

### Proveedores configurados

| Proveedor | Driver | Variable de entorno | Modelos destacados |
|---|---|---|---|
| OpenAI | `openai` | `OPENAI_API_KEY` | gpt-4o, gpt-4-turbo |
| Gemini | `gemini` | `GEMINI_API_KEY` | gemini-2.0-flash (imágenes) |
| Kimi (Moonshot) | `openai`* | `KIMI_API_KEY` | kimi-k2.5, kimi-k2.6, moonshot-v1-32k |
| Groq | `groq` | `GROQ_API_KEY` | llama3, mixtral |
| DeepSeek | `deepseek` | `DEEPSEEK_API_KEY` | deepseek-chat |
| Anthropic | `anthropic` | `ANTHROPIC_API_KEY` | claude-3-5-sonnet |

_*Kimi usa el driver `openai` apuntando a `https://api.moonshot.ai/v1` — API 100% compatible._

### Proveedores por defecto

| Operación | Proveedor |
|---|---|
| Texto | `openai` |
| Imágenes | `gemini` |
| Audio / Transcripción | `openai` |
| Embeddings | `openai` |
| Reranking | `cohere` |

### Kimi AI

Kimi es el modelo de Moonshot AI, configurado como proveedor alternativo de texto con soporte para contextos largos.

**Variables necesarias en `.env`:**

```env
KIMI_API_KEY=sk-...
KIMI_BASE_URL=https://api.moonshot.ai/v1
```

**Uso en código:**

```php
use Laravel\Ai\Facades\Ai;

$response = Ai::provider('kimi')
    ->text('moonshot-v1-32k')
    ->generate('Tu prompt aquí');
```

**Modelos disponibles:**

| Modelo | Contexto | Uso recomendado |
|---|---|---|
| `moonshot-v1-8k` | 8 K tokens | Respuestas cortas, rápido |
| `moonshot-v1-32k` | 32 K tokens | Uso general |
| `moonshot-v1-128k` | 128 K tokens | Documentos largos |
| `kimi-k2.5` | — | Razonamiento avanzado |
| `kimi-k2.6` | — | Última versión estable |
| `kimi-k2-thinking` | — | Modo de razonamiento profundo |

> **Nota:** La temperatura máxima de Kimi es `1.0` (no `2.0` como OpenAI). No soporta `tool_choice=required`.

---

## Autopublicación en redes sociales

Cada vez que una entrada pasa a **Publicado** (acción manual en Filament, self-heal del modelo o el scheduler `posts:publish-scheduled`), se dispara el evento `PostPublished` y se encola el job `PublishPostToSocialMedia`, que hace fan-out a todas las redes activas en `config/services.php`:

```php
'social' => [
    'networks' => ['discord'], // añade 'facebook', 'instagram', 'threads', 'x' cuando existan
],
```

El resultado de cada envío se registra en la tabla `social_posts` (único por `post_id + network`) con el ID externo y la URL del mensaje, lo que garantiza idempotencia (nunca se repite una publicación) y permite editar o borrar el contenido externo en el futuro.

### Discord (vía Webhook)

Discord se integra con un **webhook de canal** — gratis, sin bot ni revisión de app.

**1. Crear el webhook en Discord**

1. En tu servidor, entra al canal de anuncios → **Editar canal** → **Integraciones** → **Webhooks** → **Nuevo webhook**.
2. Nómbralo (ej. "Mundo Futuro") y dale un avatar si quieres.
3. Copia la URL. Tiene el formato `https://discord.com/api/webhooks/{id}/{token}`.

**2. Configurar la app**

```env
# .env
DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/123456/abc-token
```

**3. Formato del mensaje**

El publisher envía un **embed** con:

| Campo | Contenido |
|---|---|
| `title` | Título del post (limitado a 255) |
| `description` | `excerpt` o primer extracto del body (limitado a ~3.900 chars) |
| `url` | URL pública del post (`/publicaciones/{slug}` o `/tutoriales/{slug}`) |
| `image` | Imagen de portada (`cover_image_path`) si existe |
| `timestamp` | `published_at` en ISO 8601 |

La petición usa `?wait=true`, por lo que Discord responde con el objeto del mensaje y se guardan `external_id` + URL (`https://discord.com/channels/{guild}/{channel}/{message}`).

**4. Publicación en cola**

Requiere `queue:work` o `schedule:work` corriendo (en producción, el listener de `schedule:run`). Si el job falla, reintenta 3 veces con backoff de 30s y 120s y registra el error en el log/Nightwatch.

**Notas importantes**

- La URL del webhook es una credencial: solo puede publicar en ese canal. Si se filtra, elimínala en Discord y crea otra.
- Si el canal se borra, el webhook muere con 404 permanente; el job lo reportará y habrá que configurar otro.
- Si `DISCORD_WEBHOOK_URL` no está configurada, el job termina sin hacer nada (el registro igual se crea vacío).
- El límite de Discord de 2.000 caracteres aplica al `content`; este integración usa `embed.description` (hasta ~4.000), por lo que el resumen largo no se trunca en un archivo adjunto.

**5. Probar manualmente**

```bash
# Sin cola: publica inline al guardar un post
php artisan tinker --execute 'App\Models\Post::factory()->published()->create();'

# Con cola: verifica el job encolado
php artisan queue:work --once
```

**Archivos implicados**

| Archivo | Rol |
|---|---|
| `app/Models/Post.php` | Detecta la transición a publicado y encola el job |
| `app/Events/PostPublished.php` | Evento con el post publicado |
| `app/Jobs/PublishPostToSocialMedia.php` | Fan-out a redes activas con reintentos |
| `app/Contracts/SocialPublisher.php` | Contrato para integrar una red nueva |
| `app/Services/Social/SocialPublisherRegistry.php` | Resuelve el publisher por red |
| `app/Services/Social/DiscordPublisher.php` | Envío del embed vía webhook |
| `app/Models/SocialPost.php` | Registro de mensajes publicados por red |
| `config/services.php` | `DISCORD_WEBHOOK_URL` y lista de redes activas |

**Tests:** `tests/Feature/Social/DiscordAutopublishTest.php`.

### Añadir una red nueva

1. Crea una clase que implemente `App\Contracts\SocialPublisher` (método `publish(Post $post): array{external_id, external_url}`).
2. Regístrala en el mapa de `SocialPublisherRegistry::NETWORKS`.
3. Añade el nombre a `config('services.social.networks')` y sus credenciales en `config/services.php`.
4. El resto (detección del evento, cola, idempotencia, tabla) es compartido.

---

## Convenciones de desarrollo

- **PHP**: PHP 8.4, constructor property promotion, tipos explícitos en todos los métodos.
- **Formato**: `vendor/bin/pint --dirty` antes de cada commit.
- **Controladores**: thin controllers, queries directas sin service layer para operaciones simples.
- **Vistas**: `x-layouts.public` como wrapper, Alpine.js `isDark` para theming inline, sin JavaScript custom adicional.
- **Tests**: Pest 4. Correr con `php artisan test --compact`.
