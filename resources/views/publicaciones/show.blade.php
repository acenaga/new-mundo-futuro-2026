<x-layouts.public :title="$post->title . ' — ' . config('app.name')">

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
                class="font-display mb-8 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest transition-colors"
                :class="isDark ? 'text-[#9999b3] hover:text-[#f4bf27]' : 'text-[#4a4a6a] hover:text-[#110090]'">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Publicaciones
            </a>

            {{-- Category + meta --}}
            <div class="mb-5 flex flex-wrap items-center gap-3">
                @if ($post->category)
                    <span class="font-display rounded-md bg-[#f4bf27] px-2.5 py-1 text-xs font-bold uppercase tracking-widest text-[#342600]">
                        {{ $post->category->name }}
                    </span>
                @endif
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
                <p class="font-body mb-8 text-lg leading-relaxed"
                    :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                    {{ $post->excerpt }}
                </p>
            @endif

            {{-- Author + tags --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-t pt-6"
                :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200/60'">
                <div class="flex items-center gap-3">
                    <div class="font-display flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold"
                        :class="isDark ? 'bg-[#3b2068] text-[#d3bbff]' : 'bg-[#e8e8ff] text-[#110090]'">
                        {{ strtoupper(substr($post->author?->name ?? 'M', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-display text-sm font-semibold"
                            :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                            {{ $post->author?->name ?? 'Mundo Futuro' }}
                        </p>
                        <p class="font-display text-xs" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                            Autor
                        </p>
                    </div>
                </div>

                @if ($post->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('publicaciones', ['tag' => $tag->slug]) }}"
                                class="font-body rounded-md px-2.5 py-1 text-xs transition-colors"
                                :class="isDark
                                    ? 'bg-[#21212d] text-[#9999b3] hover:text-[#e2e2f0]'
                                    : 'bg-[#eaeaf5] text-[#4a4a6a] hover:text-[#12121d]'">
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
            <div class="-mt-8 overflow-hidden rounded-2xl shadow-ambient">
                <img src="{{ Storage::url($post->cover_image_path) }}"
                    alt="{{ $post->title }}"
                    class="h-64 w-full object-cover lg:h-96">
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════
         BODY
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mx-auto max-w-4xl px-6 py-14 lg:px-8 lg:py-20">
        <div class="font-body prose-lg max-w-none leading-relaxed"
            :class="isDark
                ? 'text-[#c8c8e0] [&_h2]:text-[#e2e2f0] [&_h3]:text-[#e2e2f0] [&_strong]:text-[#e2e2f0] [&_a]:text-[#c1c1ff]'
                : 'text-[#2a2a3a] [&_h2]:text-[#12121d] [&_h3]:text-[#12121d] [&_strong]:text-[#12121d] [&_a]:text-[#110090]'">
            {!! nl2br(e($post->body)) !!}
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         RELATED POSTS
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($related->isNotEmpty())
        <div class="border-t" :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200'">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">

                <div class="mb-10 flex items-end justify-between">
                    <div>
                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                            :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                            Más en {{ $post->category?->name }}
                        </span>
                        <h2 class="font-display mt-1 text-2xl font-bold tracking-tight"
                            :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                            También te puede interesar
                        </h2>
                    </div>
                    <a href="{{ route('publicaciones') }}"
                        class="font-display hidden text-xs font-semibold uppercase tracking-widest transition-colors sm:block"
                        :class="isDark ? 'text-[#9999b3] hover:text-[#f4bf27]' : 'text-[#4a4a6a] hover:text-[#110090]'">
                        Ver todas →
                    </a>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $relatedPost)
                        <article class="group flex flex-col overflow-hidden rounded-xl transition-colors"
                            :class="isDark ? 'bg-[#1b1b25] hover:bg-[#21212d]' : 'bg-[#eaeaf5] hover:bg-[#e0e0f0]'">

                            <div class="flex aspect-video shrink-0 items-center justify-center overflow-hidden"
                                :class="isDark ? 'bg-[#21212d]' : 'bg-[#e0e0f0]'">
                                @if ($relatedPost->cover_image_path)
                                    <img src="{{ Storage::url($relatedPost->cover_image_path) }}"
                                        alt="{{ $relatedPost->title }}"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <svg class="h-10 w-10 opacity-10"
                                        :class="isDark ? 'text-[#c1c1ff]' : 'text-[#110090]'"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                    </svg>
                                @endif
                            </div>

                            <div class="flex flex-1 flex-col gap-3 p-5">
                                <div class="flex items-center gap-2">
                                    @if ($relatedPost->category)
                                        <span class="font-display text-xs font-semibold uppercase tracking-[0.1em]"
                                            :class="isDark ? 'text-[#f4bf27]' : 'text-[#110090]'">
                                            {{ $relatedPost->category->name }}
                                        </span>
                                    @endif
                                    <span :class="isDark ? 'text-[#3a3a55]' : 'text-[#d0d0e8]'" class="text-xs">·</span>
                                    <span class="font-display text-xs" :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                        {{ max(1, (int) ceil(str_word_count(strip_tags($relatedPost->body ?? '')) / 200)) }} min
                                    </span>
                                </div>

                                <h3 class="font-display text-base font-bold leading-snug transition-colors group-hover:text-[#f4bf27]"
                                    :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                                    {{ $relatedPost->title }}
                                </h3>

                                @if ($relatedPost->excerpt)
                                    <p class="font-body line-clamp-2 text-sm leading-relaxed"
                                        :class="isDark ? 'text-[#9999b3]' : 'text-[#4a4a6a]'">
                                        {{ $relatedPost->excerpt }}
                                    </p>
                                @endif

                                <div class="mt-auto border-t pt-4"
                                    :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200/60'">
                                    <a href="{{ route('publicaciones.show', $relatedPost) }}"
                                        class="font-display text-xs font-semibold transition-colors"
                                        :class="isDark ? 'text-[#c1c1ff] hover:text-[#f4bf27]' : 'text-[#110090] hover:text-[#4c2e84]'">
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

</x-layouts.public>
