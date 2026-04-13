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
                        <div class="h-px w-6" :class="isDark ? 'bg-[#c1c1ff]' : 'bg-[#4c2e84]'"></div>
                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                            :class="isDark ? 'text-[#c1c1ff]' : 'text-[#4c2e84]'">
                            ✦ Aprende Paso a Paso
                        </span>
                    </div>
                    <h1 class="font-display text-5xl font-bold tracking-tight lg:text-6xl"
                        :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                        TUTORIALES
                    </h1>
                    <p class="font-body max-w-md text-sm leading-relaxed"
                        :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                        Guías prácticas y paso a paso para dominar las tecnologías que definen la próxima generación de la web.
                    </p>
                </div>

                {{-- Tag filter --}}
                @if ($tags->isNotEmpty())
                    <div class="flex flex-col gap-3 border-t pt-6"
                        :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200/60'">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-display mr-1 text-xs font-semibold uppercase tracking-widest"
                                :class="isDark ? 'text-[#3a3a55]' : 'text-[#c8c8e0]'">
                                Etiqueta
                            </span>
                            <a href="{{ route('tutoriales') }}"
                                class="font-display rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-widest transition-colors"
                                :class="{{ !$tagSlug ? 'true' : 'false' }}
                                    ? 'bg-[#f4bf27] text-[#342600]'
                                    : (isDark ? 'bg-[#1b1b25] text-[#9999b3] hover:text-[#e2e2f0]' : 'bg-white/60 text-[#4a4a6a] hover:text-[#12121d]')">
                                Todos
                            </a>
                            @foreach ($tags as $tag)
                                <a href="{{ route('tutoriales', ['tag' => $tag->slug]) }}"
                                    class="font-display rounded-lg px-3 py-1.5 text-xs font-bold uppercase tracking-widest transition-colors"
                                    :class="{{ $tagSlug === $tag->slug ? 'true' : 'false' }}
                                        ? 'bg-[#f4bf27] text-[#342600]'
                                        : (isDark ? 'bg-[#1b1b25] text-[#9999b3] hover:text-[#e2e2f0]' : 'bg-white/60 text-[#4a4a6a] hover:text-[#12121d]')">
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

            @php $featured = $tutorials->first(); $rest = $tutorials->slice(1); @endphp

            {{-- ── Featured tutorial ──────────────────────────────────── --}}
            <article class="clip-hex-corner relative mb-8 overflow-hidden rounded-xl lg:mb-12"
                :class="isDark ? 'bg-[#1b1b25]' : 'bg-[#eaeaf5]'">

                <div class="pointer-events-none absolute inset-0 bg-gradient-to-br"
                    :class="isDark ? 'from-[#3b2068]/15 via-transparent' : 'from-[#d3bbff]/25 via-transparent'">
                </div>

                <div class="relative grid items-center gap-0 lg:grid-cols-5">

                    {{-- Text --}}
                    <div class="flex flex-col gap-5 p-8 lg:col-span-3 lg:p-12">

                        <div class="flex flex-wrap items-center gap-3">
                            <span class="font-display rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-widest"
                                :class="isDark ? 'bg-[#3b2068] text-[#d3bbff]' : 'bg-[#e8e8ff] text-[#4c2e84]'">
                                Tutorial Destacado
                            </span>
                            <span class="font-display text-xs"
                                :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                {{ $featured->published_at?->translatedFormat('d M, Y') ?? '—' }}
                                <span class="mx-1 opacity-40">·</span>
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

                        <div class="mt-auto flex items-center gap-4 pt-2">
                            <a href="#"
                                class="font-display flex items-center gap-2 rounded-lg bg-[#f4bf27] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[#342600] transition-all hover:brightness-110">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5.14v14l11-7-11-7z" />
                                </svg>
                                Ver Tutorial
                            </a>
                        </div>
                    </div>

                    {{-- Thumbnail --}}
                    <div class="relative flex aspect-video items-center justify-center overflow-hidden lg:col-span-2 lg:aspect-auto lg:self-stretch"
                        :class="isDark ? 'bg-[#21212d]' : 'bg-[#e0e0f0]'">
                        @if ($featured->cover_image_path)
                            <img src="{{ Storage::url($featured->cover_image_path) }}"
                                alt="{{ $featured->title }}" class="h-full w-full object-cover">
                        @else
                            <svg class="h-20 w-20 opacity-10"
                                :class="isDark ? 'text-[#c1c1ff]' : 'text-[#4c2e84]'" fill="none"
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
                    </div>

                </div>
            </article>

            {{-- ── Tutorial grid ───────────────────────────────────────── --}}
            @if ($rest->isNotEmpty())
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($rest as $tutorial)
                        <article class="group flex flex-col gap-4 overflow-hidden rounded-xl p-5 transition-colors"
                            :class="isDark ? 'bg-[#1b1b25] hover:bg-[#21212d]' : 'bg-[#eaeaf5] hover:bg-[#e0e0f0]'">

                            {{-- Thumbnail --}}
                            <div class="relative flex aspect-video items-center justify-center overflow-hidden rounded-lg"
                                :class="isDark ? 'bg-[#21212d]' : 'bg-[#e8e8ff]'">
                                @if ($tutorial->cover_image_path)
                                    <img src="{{ Storage::url($tutorial->cover_image_path) }}"
                                        alt="{{ $tutorial->title }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <svg class="h-12 w-12 opacity-20"
                                        :class="isDark ? 'text-[#c1c1ff]' : 'text-[#4c2e84]'" fill="none"
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
                            </div>

                            {{-- Content --}}
                            <div class="flex flex-1 flex-col gap-2">
                                <h3 class="font-display text-base font-bold leading-snug transition-colors group-hover:text-[#f4bf27]"
                                    :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                                    {{ $tutorial->title }}
                                </h3>

                                @if ($tutorial->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($tutorial->tags->take(3) as $tag)
                                            <span class="font-body rounded-md px-2 py-0.5 text-xs"
                                                :class="isDark ? 'bg-[#292934] text-[#9999b3]' : 'bg-white text-[#4a4a6a]'">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Footer --}}
                            <div class="mt-auto flex items-center gap-2 border-t pt-3 text-xs"
                                :class="[isDark ? 'border-[#3a3a55]/30 text-[#9999b3]' : 'border-gray-200/60 text-[#4a4a6a]']">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $tutorial->published_at?->diffForHumans() ?? '—' }}
                                <span class="ml-auto font-semibold"
                                    :class="isDark ? 'text-[#c1c1ff]' : 'text-[#4c2e84]'">
                                    {{ max(1, (int) ceil(str_word_count(strip_tags($tutorial->body ?? '')) / 200)) }} min
                                </span>
                            </div>

                        </article>
                    @endforeach
                </div>
            @endif

            {{-- ── Pagination ──────────────────────────────────────────── --}}
            @if ($tutorials->hasPages())
                <div class="mt-12 flex items-center justify-between border-t pt-6 lg:mt-16 lg:pt-8"
                    :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200'">

                    @if ($tutorials->onFirstPage())
                        <span class="font-display cursor-not-allowed text-xs font-semibold uppercase tracking-widest opacity-25"
                            :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                            ← Anteriores
                        </span>
                    @else
                        <a href="{{ $tutorials->previousPageUrl() }}"
                            class="font-display text-xs font-semibold uppercase tracking-widest transition-colors"
                            :class="isDark ? 'text-[#9999b3] hover:text-[#f4bf27]' : 'text-[#4a4a6a] hover:text-[#110090]'">
                            ← Anteriores
                        </a>
                    @endif

                    <span class="font-display text-xs tabular-nums"
                        :class="isDark ? 'text-[#3a3a55]' : 'text-[#c8c8e0]'">
                        {{ $tutorials->currentPage() }} / {{ $tutorials->lastPage() }}
                    </span>

                    @if ($tutorials->hasMorePages())
                        <a href="{{ $tutorials->nextPageUrl() }}"
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
                    <svg class="h-9 w-9 opacity-30" :class="isDark ? 'text-[#c1c1ff]' : 'text-[#4c2e84]'"
                        fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <div>
                    <p class="font-display text-sm font-semibold uppercase tracking-widest"
                        :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                        @if ($tagSlug)
                            No hay tutoriales con esta etiqueta aún.
                        @else
                            Los tutoriales se publicarán pronto.
                        @endif
                    </p>
                    @if ($tagSlug)
                        <a href="{{ route('tutoriales') }}"
                            class="font-display mt-4 inline-block text-xs font-semibold uppercase tracking-widest transition-colors"
                            :class="isDark ? 'text-[#c1c1ff] hover:text-[#f4bf27]' : 'text-[#4c2e84] hover:text-[#110090]'">
                            ← Ver todos los tutoriales
                        </a>
                    @endif
                </div>
            </div>

        @endif

    </div>

</x-layouts.public>
