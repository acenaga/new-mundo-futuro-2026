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
│   └── head.blade.php             # Meta tags, fuentes, Vite assets
├── welcome.blade.php              # Home page
├── publicaciones/
│   └── index.blade.php            # Listado de publicaciones
└── tutoriales/
    └── index.blade.php            # Listado de tutoriales
```

---

## Rutas públicas

| Método | URI | Nombre | Descripción |
|---|---|---|---|
| GET | `/` | `home` | Página de inicio |
| GET | `/publicaciones` | `publicaciones` | Todas las publicaciones (excepto tutoriales) |
| GET | `/tutoriales` | `tutoriales` | Solo tutoriales |

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
| `tutorials` | Tutoriales (aparece en `/tutoriales`, no en `/publicaciones`) |
| `noticias` | Noticias del ecosistema |
| `opinion` | Artículos de opinión |
| `analisis` | Análisis técnicos |

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

## Convenciones de desarrollo

- **PHP**: PHP 8.4, constructor property promotion, tipos explícitos en todos los métodos.
- **Formato**: `vendor/bin/pint --dirty` antes de cada commit.
- **Controladores**: thin controllers, queries directas sin service layer para operaciones simples.
- **Vistas**: `x-layouts.public` como wrapper, Alpine.js `isDark` para theming inline, sin JavaScript custom adicional.
- **Tests**: Pest 4. Correr con `php artisan test --compact`.
