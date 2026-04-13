<x-layouts.public>
    {{-- ═══════════════════════════════════════════════════════════════════
         1. HERO
    ═══════════════════════════════════════════════════════════════════ --}}
    <section class="hero-section relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="hero-blob-left absolute -left-32 -top-32 h-96 w-96 rounded-full blur-3xl"></div>
            <div class="hero-blob-right absolute -bottom-20 right-10 h-72 w-72 rounded-full blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-6 py-24 lg:px-8 lg:py-32">
            <div class="grid items-center gap-12 lg:grid-cols-2">

                <div class="flex flex-col gap-6">
                    <div class="flex items-center gap-2">
                        <div class="h-px w-6" :class="isDark ? 'bg-[#f4bf27]' : 'bg-[#110090]'"></div>
                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                            :class="isDark ? 'text-[#f4bf27]' : 'text-[#110090]'">
                            ✦ Plataforma Educativa de Próxima Generación
                        </span>
                    </div>

                    <h1 class="font-display text-5xl font-bold leading-[1.05] tracking-tight lg:text-6xl xl:text-7xl">
                        <span :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">DOMINA EL</span><br>
                        <span class="text-[#f4bf27]">DESARROLLO</span><br>
                        <span :class="isDark ? 'text-[#c1c1ff]' : 'text-[#4c2e84]'">WEB</span><br>
                        <span :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">DEL FUTURO</span>
                    </h1>

                    <p class="font-body max-w-lg text-base leading-relaxed lg:text-lg"
                        :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                        Aprende las arquitecturas más avanzadas y las herramientas que definirán la próxima década de la
                        web. De WebGL a AI-Driven Interfaces.
                    </p>

                    <div class="flex flex-wrap items-center gap-4">
                        <a href="#cursos"
                            class="font-display rounded-lg bg-[#f4bf27] px-6 py-3 text-sm font-bold text-[#342600] transition-all hover:brightness-110">
                            EXPLORAR CURSOS →
                        </a>
                        <a href="#tutoriales"
                            class="font-display rounded-lg border px-6 py-3 text-sm font-semibold transition-colors"
                            :class="isDark
                                ?
                                'border-[#3a3a55] text-[#c1c1ff] hover:border-[#c1c1ff]' :
                                'border-[#110090]/30 text-[#110090] hover:border-[#110090]'">
                            VER TUTORIALES
                        </a>
                    </div>
                </div>

                <div class="flex items-center justify-center lg:justify-end">
                    <div class="relative">
                        <div class="absolute inset-0 -m-8 rounded-full blur-3xl"
                            :class="isDark ? 'bg-[#4c2e84]/20' : 'bg-[#c1c1ff]/30'"></div>
                        <img :src="isDark ? '{{ asset('assets/img/logo-dark.svg') }}' : '{{ asset('assets/img/logo.svg') }}'" alt="Mundo Futuro"
                            class="relative z-10 h-64 w-64 object-contain drop-shadow-2xl lg:h-80 lg:w-80">
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════
         2. CURSOS DESTACADOS
    ═══════════════════════════════════════════════════════════════════ --}}
    <section id="cursos" class="mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-28">

        <div class="mb-10 flex items-end justify-between">
            <div class="flex flex-col gap-2">
                <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                    :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                    Programas Premium
                </span>
                <h2 class="font-display text-3xl font-bold tracking-tight lg:text-4xl"
                    :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                    Cursos Destacados
                </h2>
            </div>
            <a href="#" class="hidden text-xs font-semibold uppercase tracking-widest transition-colors sm:block"
                :class="isDark ? 'text-[#9999b3] hover:text-[#f4bf27]' : 'text-[#4a4a6a] hover:text-[#110090]'">
                VER CATÁLOGO COMPLETO ↗
            </a>
        </div>

        @if ($featuredCourses->isNotEmpty())
            <div class="grid gap-4 lg:grid-cols-3">

                @php $featured = $featuredCourses->first(); @endphp
                <div class="clip-hex-corner relative col-span-1 flex min-h-56 flex-col justify-end overflow-hidden rounded-xl p-6 lg:col-span-2"
                    :class="isDark ? 'bg-[#1b1b25]' : 'bg-[#eaeaf5]'">
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t"
                        :class="isDark ? 'from-[#0d0d18]/80 via-transparent' : 'from-[#eaeaf5]/90 via-transparent'">
                    </div>
                    <div class="relative z-10 flex flex-col gap-3">
                        <div class="flex flex-wrap gap-2">
                            <span
                                class="font-display rounded-md bg-[#f4bf27] px-2 py-0.5 text-xs font-bold text-[#342600]">
                                BEST SELLER
                            </span>
                            <span class="font-display rounded-md px-2 py-0.5 text-xs font-bold"
                                :class="isDark ? 'bg-[#3b2068] text-[#d3bbff]' : 'bg-[#e8e8ff] text-[#110090]'">
                                AVANZADO
                            </span>
                        </div>
                        <h3 class="font-display text-xl font-bold leading-tight lg:text-2xl"
                            :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                            {{ $featured->title }}
                        </h3>
                        <p class="line-clamp-2 text-sm" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                            {{ $featured->description }}
                        </p>
                        <div class="flex items-center justify-between pt-2">
                            <span class="font-display text-xs font-medium uppercase tracking-widest"
                                :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                Acceso Libre
                            </span>
                            <a href="#"
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#f4bf27] text-[#342600] transition-all hover:brightness-110">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    @foreach ($featuredCourses->skip(1) as $course)
                        <div class="flex flex-col gap-3 rounded-xl p-5 transition-colors"
                            :class="isDark ? 'bg-[#1b1b25] hover:bg-[#21212d]' : 'bg-[#eaeaf5] hover:bg-[#e0e0f0]'">
                            <h3 class="font-display text-base font-bold leading-tight"
                                :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                                {{ $course->title }}
                            </h3>
                            <div class="flex items-center justify-between">
                                <span class="font-display text-sm font-semibold"
                                    :class="isDark ? 'text-[#c1c1ff]' : 'text-[#110090]'">
                                    Acceso Libre
                                </span>
                                <a href="#"
                                    class="font-display text-xs font-semibold uppercase tracking-widest transition-colors"
                                    :class="isDark ? 'text-[#9999b3] hover:text-[#f4bf27]' :
                                        'text-[#4a4a6a] hover:text-[#110090]'">
                                    VER →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        @else
            <p class="text-sm" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                Los cursos se publicarán pronto. ¡Vuelve en breve!
            </p>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════
         3. TUTORIALES POPULARES
    ═══════════════════════════════════════════════════════════════════ --}}
    <section id="tutoriales" class="py-20 transition-colors lg:py-28"
        :class="isDark ? 'bg-[#1b1b25]' : 'bg-[#eaeaf5]'">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">

            <div class="mb-10 text-center">
                <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                    :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                    Aprende Paso a Paso
                </span>
                <h2 class="font-display mt-2 text-3xl font-bold tracking-tight lg:text-4xl"
                    :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                    Tutoriales Populares
                </h2>
            </div>

            @if ($popularTutorials->isNotEmpty())
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($popularTutorials as $tutorial)
                        <article class="group flex flex-col gap-4 overflow-hidden rounded-xl p-5 transition-colors"
                            :class="isDark ? 'bg-[#21212d] hover:bg-[#292934]' : 'bg-white hover:bg-gray-50'">

                            <div class="flex aspect-video items-center justify-center overflow-hidden rounded-lg"
                                :class="isDark ? 'bg-[#292934]' : 'bg-[#e8e8ff]'">
                                @if ($tutorial->cover_image_path)
                                    <img src="{{ Storage::url($tutorial->cover_image_path) }}"
                                        alt="{{ $tutorial->title }}" class="h-full w-full object-cover">
                                @else
                                    <svg class="h-12 w-12 opacity-20"
                                        :class="isDark ? 'text-[#c1c1ff]' : 'text-[#110090]'" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                @endif
                            </div>

                            <div class="flex flex-col gap-2">
                                <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                                    :class="isDark ? 'text-[#f4bf27]' : 'text-[#110090]'">
                                    {{ $tutorial->category->name ?? 'Tutorial' }}
                                </span>
                                <h3 class="font-display text-base font-bold leading-snug transition-colors group-hover:text-[#f4bf27]"
                                    :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                                    {{ $tutorial->title }}
                                </h3>
                                @if ($tutorial->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($tutorial->tags->take(2) as $tag)
                                            <span class="font-body rounded-md px-2 py-0.5 text-xs"
                                                :class="isDark ? 'bg-[#292934] text-[#9999b3]' : 'bg-[#e8e8ff] text-[#4a4a6a]'">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="mt-auto flex items-center gap-2 text-xs"
                                :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $tutorial->published_at?->diffForHumans() ?? '—' }}
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <p class="text-center text-sm" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                    Los tutoriales se publicarán pronto.
                </p>
            @endif
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════
         4. PUBLICACIONES RECIENTES
    ═══════════════════════════════════════════════════════════════════ --}}
    <section id="publicaciones" class="mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-28">
        <div class="grid gap-16 lg:grid-cols-5">

            <div class="flex flex-col gap-6 lg:col-span-2">
                <div class="flex flex-col gap-2">
                    <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                        :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                        Journal Digital
                    </span>
                    <h2 class="font-display text-3xl font-bold leading-tight tracking-tight lg:text-4xl"
                        :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                        Publicaciones<br>Recientes
                    </h2>
                </div>
                <p class="font-body text-sm leading-relaxed" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                    Perspectivas profundas sobre el ecosistema tecnológico, entrevistas con líderes de la industria y
                    análisis de tendencias.
                </p>
                @if ($recentPosts->isNotEmpty())
                    <ol class="flex flex-col gap-3">
                        @foreach ($recentPosts as $i => $post)
                            <li class="flex items-start gap-3">
                                <span class="font-display shrink-0 text-xs font-bold tabular-nums"
                                    :class="isDark ? 'text-[#3a3a55]' : 'text-[#c8c8e0]'">
                                    {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="font-display text-xs font-semibold uppercase tracking-widest"
                                    :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                    {{ Str::upper($post->title) }}
                                </span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            <div class="flex flex-col gap-10 lg:col-span-3">
                @forelse ($recentPosts as $post)
                    <article class="grid items-start gap-6 sm:grid-cols-2">
                        <div class="flex flex-col gap-3">
                            <div class="font-display flex items-center gap-2 text-xs"
                                :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                <time datetime="{{ $post->published_at?->format('Y-m-d') }}">
                                    {{ $post->published_at?->translatedFormat('d M, Y') ?? '—' }}
                                </time>
                                <span class="opacity-40">·</span>
                                <span>{{ max(1, (int) ceil(str_word_count(strip_tags($post->body ?? '')) / 200)) }} min
                                    lectura</span>
                            </div>

                            <h3 class="font-display text-lg font-bold leading-snug"
                                :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                                {{ $post->title }}
                            </h3>

                            <p class="font-body line-clamp-3 text-sm leading-relaxed"
                                :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                {{ $post->excerpt }}
                            </p>

                            @if ($post->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($post->tags->take(3) as $tag)
                                        <span class="font-body rounded-md px-2 py-0.5 text-xs"
                                            :class="isDark ? 'bg-[#1b1b25] text-[#9999b3]' : 'bg-[#eaeaf5] text-[#4a4a6a]'">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="font-display flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                        :class="isDark ? 'bg-[#3b2068] text-[#d3bbff]' : 'bg-[#e8e8ff] text-[#110090]'">
                                        {{ strtoupper(substr($post->author?->name ?? 'M', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-display text-xs font-semibold"
                                            :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                                            {{ $post->author?->name ?? 'Mundo Futuro' }}
                                        </p>
                                        <p class="font-display text-xs uppercase tracking-widest"
                                            :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                            {{ $post->category?->name ?? '' }}
                                        </p>
                                    </div>
                                </div>
                                <a href="#" class="font-display text-xs font-semibold transition-colors"
                                    :class="isDark ? 'text-[#c1c1ff] hover:text-[#f4bf27]' :
                                        'text-[#110090] hover:text-[#4c2e84]'">
                                    Leer artículo ↓
                                </a>
                            </div>
                        </div>

                        <div class="flex aspect-video items-center justify-center overflow-hidden rounded-xl sm:aspect-square"
                            :class="isDark ? 'bg-[#1b1b25]' : 'bg-[#eaeaf5]'">
                            @if ($post->cover_image_path)
                                <img src="{{ Storage::url($post->cover_image_path) }}" alt="{{ $post->title }}"
                                    class="h-full w-full object-cover">
                            @else
                                <svg class="h-12 w-12 opacity-10"
                                    :class="isDark ? 'text-[#c1c1ff]' : 'text-[#110090]'" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                </svg>
                            @endif
                        </div>
                    </article>

                    @if (!$loop->last)
                        <hr class="border-t" :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200'">
                    @endif
                @empty
                    <p class="text-sm" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                        Las publicaciones se publicarán pronto.
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════
         5. NEWSLETTER CTA
    ═══════════════════════════════════════════════════════════════════ --}}
    <section class="transition-colors" :class="isDark ? 'bg-[#1b1b25]' : 'bg-[#eaeaf5]'">
        <div class="mx-auto max-w-2xl px-6 py-20 text-center lg:px-8">
            <h2 class="font-display text-3xl font-bold tracking-tight lg:text-4xl"
                :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                ¿LISTO PARA<br>CONSTRUIR EL FUTURO?
            </h2>
            <p class="font-body mx-auto mt-4 max-w-md text-sm leading-relaxed"
                :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                Suscríbete para recibir actualizaciones semanales sobre nuevas tecnologías, tutoriales exclusivos y
                ofertas en cursos.
            </p>

            <form class="mt-8 flex flex-col items-center gap-3 sm:flex-row" onsubmit="return false;">
                <input type="email" placeholder="Tu correo electrónico"
                    class="font-body w-full flex-1 rounded-lg px-4 py-3 text-sm outline-none ring-2 ring-transparent transition-all focus:ring-[#f4bf27]"
                    :class="isDark
                        ?
                        'bg-[#21212d] text-[#e2e2f0] placeholder:text-[#9999b3]' :
                        'bg-white text-[#12121d] placeholder:text-gray-400 shadow-sm'">
                <button type="submit"
                    class="font-display w-full rounded-lg bg-[#f4bf27] px-6 py-3 text-sm font-bold text-[#342600] transition-all hover:brightness-110 sm:w-auto">
                    UNIRME AHORA
                </button>
            </form>

            <p class="font-body mt-4 text-xs" :class="isDark ? 'text-[#9999b3]/60' : 'text-gray-400'">
                Al unirte aceptas nuestras
                <a href="#" class="underline decoration-dotted hover:no-underline">Políticas de Privacidad</a>
                y
                <a href="#" class="underline decoration-dotted hover:no-underline">Términos Generales</a>.
            </p>
        </div>
    </section>
</x-layouts.public>
