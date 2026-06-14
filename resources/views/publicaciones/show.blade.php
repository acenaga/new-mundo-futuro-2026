<x-layouts.public :title="$post->title . ' — ' . config('app.name')" :description="$post->excerpt" :ogImage="$post->cover_image_url" ogType="article" :canonical="route('publicaciones.show', $post)">

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
            <a href="{{ route('publicaciones') }}"
                class="font-display mb-8 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest transition-colors text-[#4a4a6a] hover:text-[#110090] dark:text-[#9999b3] dark:hover:text-[#f4bf27]">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Publicaciones
            </a>

            {{-- Category + meta --}}
            <div class="mb-5 flex flex-wrap items-center gap-3">
                @if ($post->category)
                    <span
                        class="font-display rounded-md bg-[#f4bf27] px-2.5 py-1 text-xs font-bold uppercase tracking-widest text-[#342600]">
                        {{ $post->category->name }}
                    </span>
                @endif
                <span class="font-display text-xs text-[#4a4a6a] dark:text-[#9999b3]">
                    {{ $post->published_at?->translatedFormat('d \d\e F, Y') ?? '—' }}
                </span>
                <span class="text-xs text-[#d0d0e8] dark:text-[#3a3a55]">·</span>
                <span class="font-display text-xs text-[#4a4a6a] dark:text-[#9999b3]">
                    {{ $post->reading_time }} min de lectura
                </span>
            </div>

            {{-- Title --}}
            <h1 class="font-display mb-6 text-4xl font-bold leading-tight tracking-tight lg:text-5xl xl:text-6xl text-[#12121d] dark:text-[#e2e2f0]">
                {{ $post->title }}
            </h1>

            {{-- Excerpt --}}
            @if ($post->excerpt)
                <p class="font-body mb-8 text-lg leading-relaxed text-[#4a4a6a] dark:text-[#9999b3]">
                    {{ $post->excerpt }}
                </p>
            @endif

            {{-- Author + tags --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-t pt-6 border-gray-200/60 dark:border-[#3a3a55]/30">
                <div class="flex items-center gap-3">
                    <div class="font-display flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold bg-[#e8e8ff] text-[#110090] dark:bg-[#3b2068] dark:text-[#d3bbff]">
                        {{ strtoupper(substr($post->author?->name ?? 'M', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-display text-sm font-semibold text-[#12121d] dark:text-[#e2e2f0]">
                            {{ $post->author?->name ?? 'Mundo Futuro' }}
                        </p>
                        <p class="font-display text-xs text-[#4a4a6a] dark:text-[#9999b3]">
                            Autor
                        </p>
                    </div>
                </div>

                @if ($post->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('publicaciones', ['tag' => $tag->slug]) }}"
                                class="font-body rounded-md px-2.5 py-1 text-xs transition-colors bg-[#eaeaf5] text-[#4a4a6a] hover:text-[#12121d] dark:bg-[#21212d] dark:text-[#9999b3] dark:hover:text-[#e2e2f0]">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════
         COVER IMAGE
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($post->cover_image_path)
        <div class="mx-auto max-w-5xl px-6 lg:px-8">
            <div class="shadow-ambient -mt-8 overflow-hidden rounded-2xl">
                <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}"
                    class="h-64 w-full object-cover lg:h-96">
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════
         BODY
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mx-auto max-w-4xl px-6 py-14 lg:px-8 lg:py-20">
        <div class="font-body prose-lg max-w-none leading-relaxed text-[#2a2a3a] [&_h2]:text-[#12121d] [&_h3]:text-[#12121d] [&_strong]:text-[#12121d] [&_a]:text-[#110090] dark:text-[#c8c8e0] [&_h2]:text-[#e2e2f0] [&_h3]:text-[#e2e2f0] [&_strong]:text-[#e2e2f0] [&_a]:text-[#c1c1ff]">
            {!! \App\Support\RichContent\RichContentOutput::render($post->renderRichContent('body')) !!}
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         RELATED POSTS
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($related->isNotEmpty())
        <div class="border-t border-gray-200 dark:border-[#3a3a55]/30">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">

                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em] text-[#4a4a6a] dark:text-[#9999b3]">
                            Más en {{ $post->category?->name }}
                        </span>
                        <h2 class="font-display mt-1 text-2xl font-bold tracking-tight text-[#12121d] dark:text-[#e2e2f0]">
                            También te puede interesar
                        </h2>
                    </div>
                    <a href="{{ route('publicaciones') }}"
                        class="font-display hidden text-xs font-semibold uppercase tracking-widest transition-colors sm:block text-[#4a4a6a] hover:text-[#110090] dark:text-[#9999b3] dark:hover:text-[#f4bf27]">
                        Ver todas →
                    </a>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $relatedPost)
                        <article class="group flex flex-col overflow-hidden rounded-xl transition-colors bg-[#eaeaf5] hover:bg-[#e0e0f0] dark:bg-[#1b1b25] dark:hover:bg-[#21212d]">

                            <div class="flex aspect-video shrink-0 items-center justify-center overflow-hidden bg-[#e0e0f0] dark:bg-[#21212d]">
                                @if ($relatedPost->cover_image_path)
                                    <img src="{{ $relatedPost->cover_image_url }}" alt="{{ $relatedPost->title }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <svg class="h-10 w-10 opacity-10 text-[#110090] dark:text-[#c1c1ff]" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                    </svg>
                                @endif
                            </div>

                            <div class="flex flex-1 flex-col gap-3 p-5">
                                <div class="flex items-center gap-2">
                                    @if ($relatedPost->category)
                                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em] text-[#110090] dark:text-[#f4bf27]">
                                            {{ $relatedPost->category->name }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-[#d0d0e8] dark:text-[#3a3a55]">·</span>
                                    <span class="font-display text-xs text-[#4a4a6a] dark:text-[#9999b3]">
                                        {{ $relatedPost->reading_time }} min
                                    </span>
                                </div>

                                <h3 class="font-display text-base font-bold leading-snug transition-colors group-hover:text-[#f4bf27] text-[#12121d] dark:text-[#e2e2f0]">
                                    {{ $relatedPost->title }}
                                </h3>

                                @if ($relatedPost->excerpt)
                                    <p class="font-body line-clamp-2 text-sm leading-relaxed text-[#4a4a6a] dark:text-[#9999b3]">
                                        {{ $relatedPost->excerpt }}
                                    </p>
                                @endif

                                <div class="mt-auto border-t pt-4 border-gray-200/60 dark:border-[#3a3a55]/30">
                                    <a href="{{ route('publicaciones.show', $relatedPost) }}"
                                        class="font-display text-xs font-semibold transition-colors text-[#110090] hover:text-[#4c2e84] dark:text-[#c1c1ff] dark:hover:text-[#f4bf27]">
                                        Leer →
                                    </a>
                                </div>
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
                    '@type' => 'Article',
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
                    'url' => route('publicaciones.show', $post),
                ];

                if ($post->cover_image_url) {
                    $jsonLd['image'] = $post->cover_image_url;
                }
            @endphp
            {!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endpush

</x-layouts.public>
