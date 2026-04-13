<x-layouts.public>

    {{-- ═══════════════════════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════════════════════ --}}
    <section class="hero-section relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="hero-blob-left absolute -left-32 -top-32 h-96 w-96 rounded-full blur-3xl"></div>
            <div class="hero-blob-right absolute -bottom-10 right-0 h-64 w-64 rounded-full blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">
            <div class="flex flex-col gap-8">

                {{-- Title --}}
                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-2">
                        <div class="h-px w-6" :class="isDark ? 'bg-[#f4bf27]' : 'bg-[#110090]'"></div>
                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                            :class="isDark ? 'text-[#f4bf27]' : 'text-[#110090]'">
                            ✦ Journal Digital
                        </span>
                    </div>
                    <h1 class="font-display text-5xl font-bold tracking-tight lg:text-6xl"
                        :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                        PUBLICACIONES
                    </h1>
                    <p class="font-body max-w-md text-sm leading-relaxed"
                        :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                        Perspectivas profundas sobre el ecosistema tecnológico, entrevistas y análisis de tendencias.
                    </p>
                </div>

                {{-- Filters --}}
                @if ($categories->isNotEmpty() || $tags->isNotEmpty())
                    <div class="flex flex-col gap-3 border-t pt-6"
                        :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200/60'">

                        {{-- Category row --}}
                        @if ($categories->isNotEmpty())
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-display mr-1 text-xs font-semibold uppercase tracking-widest"
                                    :class="isDark ? 'text-[#3a3a55]' : 'text-[#c8c8e0]'">
                                    Categoría
                                </span>
                                <a href="{{ route('publicaciones', array_filter(['tag' => $tagSlug])) }}"
                                    class="font-display rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-widest transition-colors"
                                    :class="{{ !$categorySlug ? 'true' : 'false' }}
                                        ? 'bg-[#f4bf27] text-[#342600]'
                                        : (isDark ? 'bg-[#1b1b25] text-[#9999b3] hover:text-[#e2e2f0]' : 'bg-white/60 text-[#4a4a6a] hover:text-[#12121d]')">
                                    Todas
                                </a>
                                @foreach ($categories as $category)
                                    <a href="{{ route('publicaciones', array_filter(['categoria' => $category->slug, 'tag' => $tagSlug])) }}"
                                        class="font-display rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-widest transition-colors"
                                        :class="{{ $categorySlug === $category->slug ? 'true' : 'false' }}
                                            ? 'bg-[#f4bf27] text-[#342600]'
                                            : (isDark ? 'bg-[#1b1b25] text-[#9999b3] hover:text-[#e2e2f0]' : 'bg-white/60 text-[#4a4a6a] hover:text-[#12121d]')">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        {{-- Tag row --}}
                        @if ($tags->isNotEmpty())
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-display mr-1 text-xs font-semibold uppercase tracking-widest"
                                    :class="isDark ? 'text-[#3a3a55]' : 'text-[#c8c8e0]'">
                                    Etiqueta
                                </span>
                                <a href="{{ route('publicaciones', array_filter(['categoria' => $categorySlug])) }}"
                                    class="font-display rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-widest transition-colors"
                                    :class="{{ !$tagSlug ? 'true' : 'false' }}
                                        ? 'bg-[#f4bf27] text-[#342600]'
                                        : (isDark ? 'bg-[#1b1b25] text-[#9999b3] hover:text-[#e2e2f0]' : 'bg-white/60 text-[#4a4a6a] hover:text-[#12121d]')">
                                    Todas
                                </a>
                                @foreach ($tags as $tag)
                                    <a href="{{ route('publicaciones', array_filter(['tag' => $tag->slug, 'categoria' => $categorySlug])) }}"
                                        class="font-display rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-widest transition-colors"
                                        :class="{{ $tagSlug === $tag->slug ? 'true' : 'false' }}
                                            ? 'bg-[#f4bf27] text-[#342600]'
                                            : (isDark ? 'bg-[#1b1b25] text-[#9999b3] hover:text-[#e2e2f0]' : 'bg-white/60 text-[#4a4a6a] hover:text-[#12121d]')">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                    </div>
                @endif

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════
         CONTENT
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mx-auto max-w-7xl px-6 pb-20 pt-10 lg:px-8 lg:pb-28 lg:pt-12">

        @if ($posts->isNotEmpty())

            @php $featured = $posts->first(); $rest = $posts->slice(1); @endphp

            {{-- ── Featured post ──────────────────────────────────────── --}}
            <article class="clip-hex-corner relative mb-8 overflow-hidden rounded-xl lg:mb-12"
                :class="isDark ? 'bg-[#1b1b25]' : 'bg-[#eaeaf5]'">

                <div class="pointer-events-none absolute inset-0 bg-gradient-to-br"
                    :class="isDark ? 'from-[#4c2e84]/10 via-transparent' : 'from-[#c1c1ff]/20 via-transparent'">
                </div>

                <div class="relative grid items-center gap-0 lg:grid-cols-5">

                    {{-- Text --}}
                    <div class="flex flex-col gap-5 p-8 lg:col-span-3 lg:p-12">

                        <div class="flex flex-wrap items-center gap-3">
                            @if ($featured->category)
                                <span class="font-display rounded-md bg-[#f4bf27] px-2.5 py-1 text-xs font-bold uppercase tracking-widest text-[#342600]">
                                    {{ $featured->category->name }}
                                </span>
                            @endif
                            <span class="font-display text-xs"
                                :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                {{ $featured->published_at?->translatedFormat('d M, Y') ?? '—' }}
                                <span class="opacity-40 mx-1">·</span>
                                {{ max(1, (int) ceil(str_word_count(strip_tags($featured->body ?? '')) / 200)) }} min
                            </span>
                        </div>

                        <h2 class="font-display text-2xl font-bold leading-snug tracking-tight lg:text-3xl xl:text-4xl"
                            :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                            {{ $featured->title }}
                        </h2>

                        @if ($featured->excerpt)
                            <p class="font-body line-clamp-3 text-sm leading-relaxed"
                                :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                {{ $featured->excerpt }}
                            </p>
                        @endif

                        @if ($featured->tags->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($featured->tags->take(4) as $tag)
                                    <span class="font-body rounded-md px-2 py-0.5 text-xs"
                                        :class="isDark ? 'bg-[#21212d] text-[#9999b3]' : 'bg-white text-[#4a4a6a]'">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-auto flex items-center justify-between pt-2">
                            <div class="flex items-center gap-2.5">
                                <div class="font-display flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                    :class="isDark ? 'bg-[#3b2068] text-[#d3bbff]' : 'bg-[#e8e8ff] text-[#110090]'">
                                    {{ strtoupper(substr($featured->author?->name ?? 'M', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-display text-xs font-semibold"
                                        :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                                        {{ $featured->author?->name ?? 'Mundo Futuro' }}
                                    </p>
                                </div>
                            </div>
                            <a href="#"
                                class="font-display rounded-lg bg-[#f4bf27] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[#342600] transition-all hover:brightness-110">
                                Leer artículo →
                            </a>
                        </div>
                    </div>

                    {{-- Image --}}
                    <div class="flex aspect-video items-center justify-center overflow-hidden lg:col-span-2 lg:aspect-auto lg:self-stretch"
                        :class="isDark ? 'bg-[#21212d]' : 'bg-[#e0e0f0]'">
                        @if ($featured->cover_image_path)
                            <img src="{{ Storage::url($featured->cover_image_path) }}"
                                alt="{{ $featured->title }}" class="h-full w-full object-cover">
                        @else
                            <svg class="h-16 w-16 opacity-10"
                                :class="isDark ? 'text-[#c1c1ff]' : 'text-[#110090]'" fill="none"
                                viewBox="0 0 24 24" stroke-width="0.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                        @endif
                    </div>

                </div>
            </article>

            {{-- ── Post grid ───────────────────────────────────────────── --}}
            @if ($rest->isNotEmpty())
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($rest as $post)
                        <article class="group flex flex-col overflow-hidden rounded-xl transition-colors"
                            :class="isDark ? 'bg-[#1b1b25] hover:bg-[#21212d]' : 'bg-[#eaeaf5] hover:bg-[#e0e0f0]'">

                            {{-- Cover --}}
                            <div class="flex aspect-video shrink-0 items-center justify-center overflow-hidden"
                                :class="isDark ? 'bg-[#21212d]' : 'bg-[#e0e0f0]'">
                                @if ($post->cover_image_path)
                                    <img src="{{ Storage::url($post->cover_image_path) }}"
                                        alt="{{ $post->title }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <svg class="h-10 w-10 opacity-10"
                                        :class="isDark ? 'text-[#c1c1ff]' : 'text-[#110090]'" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex flex-1 flex-col gap-3 p-5">

                                <div class="flex items-center gap-2">
                                    @if ($post->category)
                                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                                            :class="isDark ? 'text-[#f4bf27]' : 'text-[#110090]'">
                                            {{ $post->category->name }}
                                        </span>
                                    @endif
                                    <span :class="isDark ? 'text-[#3a3a55]' : 'text-[#d0d0e8]'" class="text-xs">·</span>
                                    <span class="font-display text-xs"
                                        :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                        {{ max(1, (int) ceil(str_word_count(strip_tags($post->body ?? '')) / 200)) }} min
                                    </span>
                                </div>

                                <h3 class="font-display text-base font-bold leading-snug transition-colors group-hover:text-[#f4bf27]"
                                    :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                                    {{ $post->title }}
                                </h3>

                                @if ($post->excerpt)
                                    <p class="font-body line-clamp-2 text-sm leading-relaxed"
                                        :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                        {{ $post->excerpt }}
                                    </p>
                                @endif

                                @if ($post->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($post->tags->take(2) as $tag)
                                            <span class="font-body rounded-md px-2 py-0.5 text-xs"
                                                :class="isDark ? 'bg-[#292934] text-[#9999b3]' : 'bg-white text-[#4a4a6a]'">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-auto flex items-center justify-between border-t pt-4"
                                    :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200/60'">
                                    <div class="flex items-center gap-2">
                                        <div class="font-display flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                            :class="isDark ? 'bg-[#3b2068] text-[#d3bbff]' : 'bg-[#e8e8ff] text-[#110090]'">
                                            {{ strtoupper(substr($post->author?->name ?? 'M', 0, 1)) }}
                                        </div>
                                        <span class="font-display text-xs"
                                            :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                            {{ $post->published_at?->translatedFormat('d M, Y') ?? '—' }}
                                        </span>
                                    </div>
                                    <a href="#"
                                        class="font-display text-xs font-semibold transition-colors"
                                        :class="isDark ? 'text-[#c1c1ff] hover:text-[#f4bf27]' : 'text-[#110090] hover:text-[#4c2e84]'">
                                        Leer →
                                    </a>
                                </div>
                            </div>

                        </article>
                    @endforeach
                </div>
            @endif

            {{-- ── Pagination ──────────────────────────────────────────── --}}
            @if ($posts->hasPages())
                <div class="mt-12 flex items-center justify-between border-t pt-6 lg:mt-16 lg:pt-8"
                    :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200'">

                    @if ($posts->onFirstPage())
                        <span class="font-display cursor-not-allowed text-xs font-semibold uppercase tracking-widest opacity-25"
                            :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                            ← Anteriores
                        </span>
                    @else
                        <a href="{{ $posts->previousPageUrl() }}"
                            class="font-display text-xs font-semibold uppercase tracking-widest transition-colors"
                            :class="isDark ? 'text-[#9999b3] hover:text-[#f4bf27]' : 'text-[#4a4a6a] hover:text-[#110090]'">
                            ← Anteriores
                        </a>
                    @endif

                    <span class="font-display text-xs tabular-nums"
                        :class="isDark ? 'text-[#3a3a55]' : 'text-[#c8c8e0]'">
                        {{ $posts->currentPage() }} / {{ $posts->lastPage() }}
                    </span>

                    @if ($posts->hasMorePages())
                        <a href="{{ $posts->nextPageUrl() }}"
                            class="font-display text-xs font-semibold uppercase tracking-widest transition-colors"
                            :class="isDark ? 'text-[#9999b3] hover:text-[#f4bf27]' : 'text-[#4a4a6a] hover:text-[#110090]'">
                            Siguientes →
                        </a>
                    @else
                        <span class="font-display cursor-not-allowed text-xs font-semibold uppercase tracking-widest opacity-25"
                            :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                            Siguientes →
                        </span>
                    @endif

                </div>
            @endif

        @else

            {{-- ── Empty state ─────────────────────────────────────────── --}}
            <div class="flex flex-col items-center gap-6 py-32 text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full"
                    :class="isDark ? 'bg-[#1b1b25]' : 'bg-[#eaeaf5]'">
                    <svg class="h-9 w-9 opacity-30" :class="isDark ? 'text-[#c1c1ff]' : 'text-[#110090]'"
                        fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <div>
                    <p class="font-display text-sm font-semibold uppercase tracking-widest"
                        :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                        @if ($categorySlug)
                            No hay publicaciones en esta categoría aún.
                        @else
                            Las publicaciones se publicarán pronto.
                        @endif
                    </p>
                    @if ($categorySlug)
                        <a href="{{ route('publicaciones') }}"
                            class="font-display mt-4 inline-block text-xs font-semibold uppercase tracking-widest transition-colors"
                            :class="isDark ? 'text-[#c1c1ff] hover:text-[#f4bf27]' : 'text-[#110090] hover:text-[#4c2e84]'">
                            ← Ver todas las publicaciones
                        </a>
                    @endif
                </div>
            </div>

        @endif

    </div>

</x-layouts.public>
