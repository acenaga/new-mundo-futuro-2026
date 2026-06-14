<x-layouts.public :title="$tutorial->title . ' — ' . config('app.name')" :description="$tutorial->excerpt" :ogImage="$tutorial->cover_image_url" ogType="article" :canonical="route('tutoriales.show', $tutorial)">

    {{-- ═══════════════════════════════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════════════════════════════ --}}
    <section class="hero-section relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="hero-blob-left absolute -left-32 -top-32 h-96 w-96 rounded-full blur-3xl"></div>
            <div class="hero-blob-right absolute -bottom-10 right-0 h-64 w-64 rounded-full blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-4xl px-6 py-16 lg:px-8 lg:py-20">

            {{-- Back link --}}
            <a href="{{ route('tutoriales') }}"
                class="font-display mb-8 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest transition-colors text-[#4a4a6a] hover:text-[#4c2e84] dark:text-[#9999b3] dark:hover:text-[#c1c1ff]">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Tutoriales
            </a>

            {{-- Badge + meta --}}
            <div class="mb-5 flex flex-wrap items-center gap-3">
                <span class="font-display rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-widest bg-[#e8e8ff] text-[#4c2e84] dark:bg-[#3b2068] dark:text-[#d3bbff]">
                    Tutorial
                </span>
                <span class="font-display text-xs text-[#4a4a6a] dark:text-[#9999b3]">
                    {{ $tutorial->published_at?->translatedFormat('d \d\e F, Y') ?? '—' }}
                </span>
                <span class="text-xs text-[#d0d0e8] dark:text-[#3a3a55]">·</span>
                <span class="font-display text-xs text-[#4a4a6a] dark:text-[#9999b3]">
                    {{ $tutorial->reading_time }} min de lectura
                </span>
            </div>

            {{-- Title --}}
            <h1 class="font-display mb-6 text-4xl font-bold leading-tight tracking-tight lg:text-5xl xl:text-6xl text-[#12121d] dark:text-[#e2e2f0]">
                {{ $tutorial->title }}
            </h1>

            {{-- Excerpt --}}
            @if ($tutorial->excerpt)
                <p class="font-body mb-8 text-lg leading-relaxed text-[#4a4a6a] dark:text-[#9999b3]">
                    {{ $tutorial->excerpt }}
                </p>
            @endif

            {{-- Tags + play CTA --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-t pt-6 border-gray-200/60 dark:border-[#3a3a55]/30">

                @if ($tutorial->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($tutorial->tags as $tag)
                            <a href="{{ route('tutoriales', ['tag' => $tag->slug]) }}"
                                class="font-body rounded-md px-2.5 py-1 text-xs transition-colors bg-[#eaeaf5] text-[#4a4a6a] hover:text-[#4c2e84] dark:bg-[#21212d] dark:text-[#9999b3] dark:hover:text-[#c1c1ff]">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                @if ($tutorial->author)
                    <div class="flex items-center gap-2.5">
                        <div class="font-display flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold bg-[#e8e8ff] text-[#4c2e84] dark:bg-[#3b2068] dark:text-[#d3bbff]">
                            {{ strtoupper(substr($tutorial->author->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-display text-xs font-semibold text-[#12121d] dark:text-[#e2e2f0]">
                                {{ $tutorial->author->name }}
                            </p>
                            <p class="font-display text-xs text-[#4a4a6a] dark:text-[#9999b3]">
                                Instructor</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════
         VIDEO / COVER
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mx-auto max-w-5xl px-6 lg:px-8">
        <div class="shadow-ambient mt-10 overflow-hidden rounded-2xl bg-[#e8e8ff] dark:bg-[#1b1b25]">
            @if ($tutorial->youtube_embed_url)
                <div class="relative aspect-video w-full">
                    <iframe src="{{ $tutorial->youtube_embed_url }}" class="absolute inset-0 h-full w-full" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>
            @elseif ($tutorial->cover_image_path)
                <div class="relative">
                    <img src="{{ $tutorial->cover_image_url }}" alt="{{ $tutorial->title }}"
                        class="h-64 w-full object-cover lg:h-[420px]">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-[#f4bf27]/90 shadow-2xl">
                            <svg class="ml-2 h-8 w-8 text-[#342600]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5.14v14l11-7-11-7z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex h-64 w-full items-center justify-center lg:h-[420px]">
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-[#d3bbff]/30 dark:bg-[#21212d]">
                            <svg class="ml-2 h-10 w-10 text-[#4c2e84] dark:text-[#c1c1ff]"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5.14v14l11-7-11-7z" />
                            </svg>
                        </div>
                        <p class="font-display text-xs font-semibold uppercase tracking-widest text-[#4a4a6a] dark:text-[#9999b3]">
                            Video próximamente
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         CONTENIDO
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mx-auto max-w-4xl px-6 py-14 lg:px-8 lg:py-20">
        <div class="font-body prose-lg max-w-none leading-relaxed text-[#2a2a3a] [&_h2]:text-[#12121d] [&_h3]:text-[#12121d] [&_strong]:text-[#12121d] [&_a]:text-[#4c2e84] dark:text-[#c8c8e0] [&_h2]:text-[#e2e2f0] [&_h3]:text-[#e2e2f0] [&_strong]:text-[#e2e2f0] [&_a]:text-[#c1c1ff]">
            {!! \App\Support\RichContent\RichContentOutput::render($tutorial->renderRichContent('body')) !!}
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         TUTORIALES RELACIONADOS
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($related->isNotEmpty())
        <div class="border-t border-gray-200 dark:border-[#3a3a55]/30">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">

                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em] text-[#4a4a6a] dark:text-[#9999b3]">
                            Sigue aprendiendo
                        </span>
                        <h2 class="font-display mt-1 text-2xl font-bold tracking-tight text-[#12121d] dark:text-[#e2e2f0]">
                            Más tutoriales
                        </h2>
                    </div>
                    <a href="{{ route('tutoriales') }}"
                        class="font-display hidden text-xs font-semibold uppercase tracking-widest transition-colors sm:block text-[#4a4a6a] hover:text-[#4c2e84] dark:text-[#9999b3] dark:hover:text-[#c1c1ff]">
                        Ver todos →
                    </a>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $relatedTutorial)
                        <article class="group flex flex-col gap-4 overflow-hidden rounded-xl p-5 transition-colors bg-[#eaeaf5] hover:bg-[#e0e0f0] dark:bg-[#1b1b25] dark:hover:bg-[#21212d]">

                            <a href="{{ route('tutoriales.show', $relatedTutorial) }}"
                                class="relative flex aspect-video items-center justify-center overflow-hidden rounded-lg bg-[#e8e8ff] dark:bg-[#21212d]">
                                @if ($relatedTutorial->cover_image_path)
                                    <img src="{{ $relatedTutorial->cover_image_url }}" alt="{{ $relatedTutorial->title }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <svg class="h-12 w-12 opacity-20 text-[#4c2e84] dark:text-[#c1c1ff]" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                @endif
                                <div
                                    class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-[#f4bf27]/90 shadow-md">
                                        <svg class="ml-0.5 h-4 w-4 text-[#342600]" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M8 5.14v14l11-7-11-7z" />
                                        </svg>
                                    </div>
                                </div>
                            </a>

                            <div class="flex flex-1 flex-col gap-2">
                                <a href="{{ route('tutoriales.show', $relatedTutorial) }}"
                                    class="font-display text-base font-bold leading-snug transition-colors group-hover:text-[#f4bf27] text-[#12121d] dark:text-[#e2e2f0]">
                                    {{ $relatedTutorial->title }}
                                </a>

                                @if ($relatedTutorial->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($relatedTutorial->tags->take(3) as $tag)
                                            <span class="font-body rounded-md px-2 py-0.5 text-xs bg-white text-[#4a4a6a] dark:bg-[#292934] dark:text-[#9999b3]">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="mt-auto flex items-center gap-2 border-t pt-3 text-xs border-gray-200/60 text-[#4a4a6a] dark:border-[#3a3a55]/30 dark:text-[#9999b3]">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $relatedTutorial->published_at?->diffForHumans() ?? '—' }}
                                <span class="ml-auto font-semibold text-[#4c2e84] dark:text-[#c1c1ff]">
                                    {{ $relatedTutorial->reading_time }} min
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @push('head')
        <script type="application/ld+json">
            @php
                $jsonLd = [
                    '@context' => 'https://schema.org',
                    '@type' => 'TechArticle',
                    'headline' => $tutorial->title,
                    'description' => $tutorial->excerpt,
                    'datePublished' => $tutorial->published_at?->toIso8601String(),
                    'dateModified' => $tutorial->updated_at->toIso8601String(),
                    'author' => [
                        '@type' => 'Person',
                        'name' => $tutorial->author?->name ?? config('app.name'),
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => config('app.name'),
                        'url' => url('/'),
                    ],
                    'url' => route('tutoriales.show', $tutorial),
                ];

                if ($tutorial->cover_image_url) {
                    $jsonLd['image'] = $tutorial->cover_image_url;
                }
            @endphp
            {!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush

</x-layouts.public>
