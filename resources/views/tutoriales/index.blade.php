<x-layouts.public
    title="Tutoriales — {{ config('app.name') }}"
    description="Aprende desarrollo web avanzado paso a paso con tutoriales prácticos en vídeo sobre las tecnologías más demandadas."
    :canonical="route('tutoriales')"
>

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
                        <div class="h-px w-6 bg-[#4c2e84] dark:bg-[#c1c1ff]"></div>
                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em] text-[#4c2e84] dark:text-[#c1c1ff]">
                            ✦ Aprende Paso a Paso
                        </span>
                    </div>
                    <h1 class="font-display text-5xl font-bold tracking-tight lg:text-6xl text-[#12121d] dark:text-[#e2e2f0]">
                        TUTORIALES
                    </h1>
                    <p class="font-body max-w-md text-sm leading-relaxed text-[#4a4a6a] dark:text-[#9999b3]">
                        Guías prácticas y paso a paso para dominar las tecnologías que definen la próxima generación de la web.
                    </p>
                </div>

                {{-- Tag filter --}}
                @if ($tags->isNotEmpty())
                    <div class="flex flex-col gap-3 border-t pt-6 border-gray-200/60 dark:border-[#3a3a55]/30">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-display mr-1 text-xs font-semibold uppercase tracking-widest text-[#c8c8e0] dark:text-[#3a3a55]">
                                Etiqueta
                            </span>
                            <a href="{{ route('tutoriales') }}"
                                @class([
                                    'font-display rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-widest transition-colors',
                                    'bg-[#f4bf27] text-[#342600]' => !$tagSlug,
                                    'bg-white/60 text-[#4a4a6a] hover:text-[#12121d] dark:bg-[#1b1b25] dark:text-[#9999b3] dark:hover:text-[#e2e2f0]' => $tagSlug,
                                ])>
                                Todos
                            </a>
                            @foreach ($tags as $tag)
                                <a href="{{ route('tutoriales', ['tag' => $tag->slug]) }}"
                                    @class([
                                        'font-display rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-widest transition-colors',
                                        'bg-[#f4bf27] text-[#342600]' => $tagSlug === $tag->slug,
                                        'bg-white/60 text-[#4a4a6a] hover:text-[#12121d] dark:bg-[#1b1b25] dark:text-[#9999b3] dark:hover:text-[#e2e2f0]' => $tagSlug !== $tag->slug,
                                    ])>
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════
         CONTENT
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mx-auto max-w-7xl px-6 pb-20 pt-10 lg:px-8 lg:pb-28 lg:pt-12">

        @if ($tutorials->isNotEmpty())

            @php
                $featured = $tutorials->first();
                $rest = $tutorials->slice(1);
            @endphp

            {{-- ── Featured tutorial ──────────────────────────────────── --}}
            <article class="clip-hex-corner relative mb-8 overflow-hidden rounded-xl lg:mb-12 bg-[#eaeaf5] dark:bg-[#1b1b25]">

                <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#d3bbff]/25 dark:from-[#3b2068]/15 via-transparent">
                </div>

                <div class="relative grid items-center gap-0 lg:grid-cols-5">

                    {{-- Text --}}
                    <div class="flex flex-col gap-5 p-8 lg:col-span-3 lg:p-12">

                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-display rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-widest bg-[#e8e8ff] text-[#4c2e84] dark:bg-[#3b2068] dark:text-[#d3bbff]">
                                Tutorial Destacado
                            </span>
                            <span class="font-display text-xs text-[#4a4a6a] dark:text-[#9999b3]">
                                {{ $featured->published_at?->translatedFormat('d M, Y') ?? '—' }}
                                <span class="mx-1 opacity-40">·</span>
                                {{ $featured->reading_time }} min
                            </span>
                        </div>

                        <h2 class="font-display text-2xl font-bold leading-snug tracking-tight lg:text-3xl xl:text-4xl text-[#12121d] dark:text-[#e2e2f0]">
                            {{ $featured->title }}
                        </h2>

                        @if ($featured->excerpt)
                            <p class="font-body line-clamp-3 text-sm leading-relaxed text-[#4a4a6a] dark:text-[#9999b3]">
                                {{ $featured->excerpt }}
                            </p>
                        @endif

                        @if ($featured->tags->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($featured->tags->take(4) as $tag)
                                    <span class="font-body rounded-md px-2 py-0.5 text-xs bg-white text-[#4a4a6a] dark:bg-[#21212d] dark:text-[#9999b3]">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-auto flex items-center gap-4 pt-2">
                            <a href="{{ route('tutoriales.show', $featured) }}"
                                class="font-display flex items-center gap-2 rounded-lg bg-[#f4bf27] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[#342600] transition-all hover:brightness-110">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5.14v14l11-7-11-7z" />
                                </svg>
                                Ver Tutorial
                            </a>
                        </div>
                    </div>

                    {{-- Thumbnail --}}
                    <a href="{{ route('tutoriales.show', $featured) }}"
                        class="relative flex aspect-video items-center justify-center overflow-hidden lg:col-span-2 lg:aspect-auto lg:self-stretch bg-[#e0e0f0] dark:bg-[#21212d]">
                        @php $thumb = $featured->youtube_thumbnail_url ?? $featured->cover_image_url; @endphp
                        @if ($thumb)
                            <img src="{{ $thumb }}" alt="{{ $featured->title }}"
                                class="h-full w-full object-cover transition-transform duration-500 hover:scale-105">
                        @else
                            <svg class="h-20 w-20 opacity-10 text-[#4c2e84] dark:text-[#c1c1ff]" fill="none"
                                viewBox="0 0 24 24" stroke-width="0.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        @endif
                        {{-- Play overlay --}}
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[#f4bf27]/90 shadow-lg transition-transform hover:scale-110">
                                <svg class="ml-1 h-6 w-6 text-[#342600]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5.14v14l11-7-11-7z" />
                                </svg>
                            </div>
                        </div>
                    </a>

                </div>
            </article>

            {{-- ── Tutorial grid ───────────────────────────────────────── --}}
            @if ($rest->isNotEmpty())
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($rest as $tutorial)
                        <article class="group flex flex-col gap-4 overflow-hidden rounded-xl p-5 transition-colors bg-[#eaeaf5] hover:bg-[#e0e0f0] dark:bg-[#1b1b25] dark:hover:bg-[#21212d]">

                            {{-- Thumbnail --}}
                            <a href="{{ route('tutoriales.show', $tutorial) }}"
                                class="relative flex aspect-video items-center justify-center overflow-hidden rounded-lg bg-[#e8e8ff] dark:bg-[#21212d]">
                                @php $thumb = $tutorial->youtube_thumbnail_url ?? $tutorial->cover_image_url; @endphp
                                @if ($thumb)
                                    <img src="{{ $thumb }}" alt="{{ $tutorial->title }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <svg class="h-12 w-12 opacity-20 text-[#4c2e84] dark:text-[#c1c1ff]" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                @endif
                                {{-- Play button --}}
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#f4bf27]/90 shadow-md">
                                        <svg class="ml-0.5 h-4 w-4 text-[#342600]" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5.14v14l11-7-11-7z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>

                            {{-- Content --}}
                            <div class="flex flex-1 flex-col gap-2">
                                <a href="{{ route('tutoriales.show', $tutorial) }}"
                                    class="font-display text-base font-bold leading-snug transition-colors group-hover:text-[#f4bf27] text-[#12121d] dark:text-[#e2e2f0]">
                                    {{ $tutorial->title }}
                                </a>

                                @if ($tutorial->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($tutorial->tags->take(3) as $tag)
                                            <span class="font-body rounded-md px-2 py-0.5 text-xs bg-white text-[#4a4a6a] dark:bg-[#292934] dark:text-[#9999b3]">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Footer --}}
                            <div class="mt-auto flex items-center gap-2 border-t pt-3 text-xs border-gray-200/60 text-[#4a4a6a] dark:border-[#3a3a55]/30 dark:text-[#9999b3]">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $tutorial->published_at?->diffForHumans() ?? '—' }}
                                <span class="ml-auto font-semibold text-[#4c2e84] dark:text-[#c1c1ff]">
                                    {{ $tutorial->reading_time }} min
                                </span>
                            </div>

                        </article>
                    @endforeach
                </div>
            @endif

            {{-- ── Pagination ──────────────────────────────────────────── --}}
            @if ($tutorials->hasPages())
                <div class="mt-12 flex items-center justify-between border-t pt-6 lg:mt-16 lg:pt-8 border-gray-200 dark:border-[#3a3a55]/30">

                    @if ($tutorials->onFirstPage())
                        <span class="font-display cursor-not-allowed text-xs font-semibold uppercase tracking-widest opacity-25 text-[#4a4a6a] dark:text-[#9999b3]">
                            ← Anteriores
                        </span>
                    @else
                        <a href="{{ $tutorials->previousPageUrl() }}"
                            class="font-display text-xs font-semibold uppercase tracking-widest transition-colors text-[#4a4a6a] hover:text-[#110090] dark:text-[#9999b3] dark:hover:text-[#f4bf27]">
                            ← Anteriores
                        </a>
                    @endif

                    <span class="font-display text-xs tabular-nums text-[#c8c8e0] dark:text-[#3a3a55]">
                        {{ $tutorials->currentPage() }} / {{ $tutorials->lastPage() }}
                    </span>

                    @if ($tutorials->hasMorePages())
                        <a href="{{ $tutorials->nextPageUrl() }}"
                            class="font-display text-xs font-semibold uppercase tracking-widest transition-colors text-[#4a4a6a] hover:text-[#110090] dark:text-[#9999b3] dark:hover:text-[#f4bf27]">
                            Siguientes →
                        </a>
                    @else
                        <span class="font-display cursor-not-allowed text-xs font-semibold uppercase tracking-widest opacity-25 text-[#4a4a6a] dark:text-[#9999b3]">
                            Siguientes →
                        </span>
                    @endif

                </div>
            @endif

        @else

            {{-- ── Empty state ─────────────────────────────────────────── --}}
            <div class="flex flex-col items-center gap-6 py-32 text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-[#eaeaf5] dark:bg-[#1b1b25]">
                    <svg class="h-9 w-9 opacity-30 text-[#4c2e84] dark:text-[#c1c1ff]"
                        fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <div>
                    <p class="font-display text-sm font-semibold uppercase tracking-widest text-[#4a4a6a] dark:text-[#9999b3]">
                        @if ($tagSlug)
                            No hay tutoriales con esta etiqueta aún.
                        @else
                            Los tutoriales se publicarán pronto.
                        @endif
                    </p>
                    @if ($tagSlug)
                        <a href="{{ route('tutoriales') }}"
                            class="font-display mt-4 inline-block text-xs font-semibold uppercase tracking-widest transition-colors text-[#4c2e84] hover:text-[#110090] dark:text-[#c1c1ff] dark:hover:text-[#f4bf27]">
                            ← Ver todos los tutoriales
                        </a>
                    @endif
                </div>
            </div>

        @endif

    </div>

</x-layouts.public>
