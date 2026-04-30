<x-layouts.public :title="$post->title . ' — ' . config('app.name')" :description="$post->excerpt" :ogImage="$post->cover_image_url" ogType="article" :canonical="route('tutoriales.show', $post)">

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
                class="font-display mb-8 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest transition-colors"
                :class="isDark ? 'text-[#9999b3] hover:text-[#c1c1ff]' : 'text-[#4a4a6a] hover:text-[#4c2e84]'">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Tutoriales
            </a>

            {{-- Badge + meta --}}
            <div class="mb-5 flex flex-wrap items-center gap-3">
                <span class="font-display rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-widest"
                    :class="isDark ? 'bg-[#3b2068] text-[#d3bbff]' : 'bg-[#e8e8ff] text-[#4c2e84]'">
                    Tutorial
                </span>
                <span class="font-display text-xs" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                    {{ $post->published_at?->translatedFormat('d \d\e F, Y') ?? '—' }}
                </span>
                <span :class="isDark ? 'text-[#3a3a55]' : 'text-[#d0d0e8]'" class="text-xs">·</span>
                <span class="font-display text-xs" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                    {{ max(1, (int) ceil(str_word_count(strip_tags($post->body ?? '')) / 200)) }} min de lectura
                </span>
            </div>

            {{-- Title --}}
            <h1 class="font-display mb-6 text-4xl font-bold leading-tight tracking-tight lg:text-5xl xl:text-6xl"
                :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                {{ $post->title }}
            </h1>

            {{-- Excerpt --}}
            @if ($post->excerpt)
                <p class="font-body mb-8 text-lg leading-relaxed" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                    {{ $post->excerpt }}
                </p>
            @endif

            {{-- Tags + play CTA --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-t pt-6"
                :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200/60'">

                @if ($post->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('tutoriales', ['tag' => $tag->slug]) }}"
                                class="font-body rounded-md px-2.5 py-1 text-xs transition-colors"
                                :class="isDark
                                    ?
                                    'bg-[#21212d] text-[#9999b3] hover:text-[#c1c1ff]' :
                                    'bg-[#eaeaf5] text-[#4a4a6a] hover:text-[#4c2e84]'">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                @if ($post->author)
                    <div class="flex items-center gap-2.5">
                        <div class="font-display flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                            :class="isDark ? 'bg-[#3b2068] text-[#d3bbff]' : 'bg-[#e8e8ff] text-[#4c2e84]'">
                            {{ strtoupper(substr($post->author->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-display text-xs font-semibold"
                                :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                                {{ $post->author->name }}
                            </p>
                            <p class="font-display text-xs" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
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
    @php
        $youtubeEmbed = null;
        if ($post->video_url) {
            preg_match(
                '/(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
                $post->video_url,
                $m,
            );
            $youtubeEmbed = isset($m[1]) ? 'https://www.youtube.com/embed/' . $m[1] : null;
        }
    @endphp

    <div class="mx-auto max-w-5xl px-6 lg:px-8">
        <div class="shadow-ambient mt-10 overflow-hidden rounded-2xl"
            :class="isDark ? 'bg-[#1b1b25]' : 'bg-[#e8e8ff]'">
            @if ($youtubeEmbed)
                <div class="relative aspect-video w-full">
                    <iframe src="{{ $youtubeEmbed }}" class="absolute inset-0 h-full w-full" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>
            @elseif ($post->cover_image_path)
                <div class="relative">
                    <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}"
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
                        <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full"
                            :class="isDark ? 'bg-[#21212d]' : 'bg-[#d3bbff]/30'">
                            <svg class="ml-2 h-10 w-10" :class="isDark ? 'text-[#c1c1ff]' : 'text-[#4c2e84]'"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5.14v14l11-7-11-7z" />
                            </svg>
                        </div>
                        <p class="font-display text-xs font-semibold uppercase tracking-widest"
                            :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
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
        <div class="font-body prose-lg max-w-none leading-relaxed"
            :class="isDark
                ?
                'text-[#c8c8e0] [&_h2]:text-[#e2e2f0] [&_h3]:text-[#e2e2f0] [&_strong]:text-[#e2e2f0] [&_a]:text-[#c1c1ff]' :
                'text-[#2a2a3a] [&_h2]:text-[#12121d] [&_h3]:text-[#12121d] [&_strong]:text-[#12121d] [&_a]:text-[#4c2e84]'">
            {!! str($post->body)->sanitizeHtml() !!}
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         TUTORIALES RELACIONADOS
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($related->isNotEmpty())
        <div class="border-t" :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200'">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">

                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                            :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                            Sigue aprendiendo
                        </span>
                        <h2 class="font-display mt-1 text-2xl font-bold tracking-tight"
                            :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                            Más tutoriales
                        </h2>
                    </div>
                    <a href="{{ route('tutoriales') }}"
                        class="font-display hidden text-xs font-semibold uppercase tracking-widest transition-colors sm:block"
                        :class="isDark ? 'text-[#9999b3] hover:text-[#c1c1ff]' : 'text-[#4a4a6a] hover:text-[#4c2e84]'">
                        Ver todos →
                    </a>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $tutorial)
                        <article class="group flex flex-col gap-4 overflow-hidden rounded-xl p-5 transition-colors"
                            :class="isDark ? 'bg-[#1b1b25] hover:bg-[#21212d]' : 'bg-[#eaeaf5] hover:bg-[#e0e0f0]'">

                            <a href="{{ route('tutoriales.show', $tutorial) }}"
                                class="relative flex aspect-video items-center justify-center overflow-hidden rounded-lg"
                                :class="isDark ? 'bg-[#21212d]' : 'bg-[#e8e8ff]'">
                                @if ($tutorial->cover_image_path)
                                    <img src="{{ $tutorial->cover_image_url }}"
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
                                <a href="{{ route('tutoriales.show', $tutorial) }}"
                                    class="font-display text-base font-bold leading-snug transition-colors group-hover:text-[#f4bf27]"
                                    :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                                    {{ $tutorial->title }}
                                </a>

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

                            <div class="mt-auto flex items-center gap-2 border-t pt-3 text-xs"
                                :class="[isDark ? 'border-[#3a3a55]/30 text-[#9999b3]' : 'border-gray-200/60 text-[#4a4a6a]']">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $tutorial->published_at?->diffForHumans() ?? '—' }}
                                <span class="ml-auto font-semibold"
                                    :class="isDark ? 'text-[#c1c1ff]' : 'text-[#4c2e84]'">
                                    {{ max(1, (int) ceil(str_word_count(strip_tags($tutorial->body ?? '')) / 200)) }}
                                    min
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
                    'headline' => $post->title,
                    'description' => $post->excerpt,
                    'datePublished' => $post->published_at?->toIso8601String(),
                    'dateModified' => $post->updated_at->toIso8601String(),
                    'author' => [
                        '@type' => 'Person',
                        'name' => $post->author?->name ?? config('app.name'),
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => config('app.name'),
                        'url' => url('/'),
                    ],
                    'url' => route('tutoriales.show', $post),
                ];

                if ($post->cover_image_url) {
                    $jsonLd['image'] = $post->cover_image_url;
                }
            @endphp
            {!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush

</x-layouts.public>
