@props(['resource'])

<article class="group flex h-full flex-col rounded-2xl border p-5 transition hover:-translate-y-1 hover:shadow-xl"
    :class="isDark ? 'border-[#3a3a55] bg-[#1b1b25] hover:border-[#f4bf27]/50' : 'border-gray-200 bg-white hover:border-[#110090]/30'">
    <div class="flex items-start gap-3">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-[#110090] font-display text-sm font-bold text-[#f4bf27]">
            @if ($resource->logo_url)
                <img src="{{ $resource->logo_url }}" alt="" class="h-full w-full object-contain p-1.5">
            @else
                {{ str($resource->name)->substr(0, 2)->upper() }}
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-[#f4bf27]">{{ $resource->category->name }}</p>
            <h2 class="font-display text-lg font-bold" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">{{ $resource->name }}</h2>
        </div>
    </div>

    <p class="mt-4 text-sm leading-6" :class="isDark ? 'text-[#b8b8ca]' : 'text-[#4a4a6a]'">{{ $resource->summary }}</p>

    <div class="mt-4 flex flex-wrap gap-2">
        <span class="rounded-full bg-[#110090]/10 px-2.5 py-1 text-xs font-semibold text-[#110090] dark:bg-[#c1c1ff]/15 dark:text-[#c1c1ff]">{{ $resource->pricing->label() }}</span>
        @if ($resource->no_card_required)
            <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">Sin tarjeta</span>
        @endif
        @foreach ($resource->technologies->take(3) as $technology)
            <span class="rounded-full px-2.5 py-1 text-xs" :class="isDark ? 'bg-[#292934] text-[#9999b3]' : 'bg-gray-100 text-gray-600'">{{ $technology->name }}</span>
        @endforeach
    </div>

    <div class="mt-auto flex items-center justify-between gap-3 pt-5 text-xs" :class="isDark ? 'text-[#9999b3]' : 'text-gray-500'">
        <span>Verificado {{ $resource->last_verified_at?->diffForHumans() ?? 'pendiente' }}</span>
        <a href="{{ route('recursos.show', $resource) }}" class="font-semibold text-[#110090] dark:text-[#c1c1ff]">Ver ficha <span aria-hidden="true">→</span></a>
    </div>
</article>
