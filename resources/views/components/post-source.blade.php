@props(['post'])

@if ($post->hasSource())
    <aside class="mx-auto max-w-4xl px-6 pb-14 lg:px-8 lg:pb-20" data-post-source>
        <div class="rounded-xl border p-6 border-gray-200/60 bg-[#eaeaf5]/60 dark:border-[#3a3a55]/30 dark:bg-[#1b1b25]">
            <p class="font-display mb-2 text-xs font-semibold uppercase tracking-wider text-[#4a4a6a] dark:text-[#9999b3]">
                Fuente
            </p>
            <p class="font-body text-sm leading-relaxed text-[#2a2a3a] dark:text-[#c8c8e0]">
                Este artículo está basado en
                @if ($post->source_title)
                    <em>{{ $post->source_title }}</em>@if ($post->source_author || $post->source_site),@endif
                @endif
                @if ($post->source_author)
                    de {{ $post->source_author }}@if ($post->source_site),@endif
                @endif
                @if ($post->source_site)
                    publicado en {{ $post->source_site }}
                @endif
                @if ($post->source_published_at)
                    el {{ $post->source_published_at->translatedFormat('d \d\e F, Y') }}
                @endif.
                <a href="{{ $post->source_url }}" target="_blank" rel="noopener noreferrer nofollow" class="font-semibold underline underline-offset-2 text-[#110090] dark:text-[#c1c1ff]">
                    Leer el original
                </a>
            </p>
        </div>
    </aside>
@endif
