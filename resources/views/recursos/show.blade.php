<x-layouts.public :title="$resource->name . ' — Recursos — ' . config('app.name')" :description="$resource->summary" :ogImage="$resource->logo_url" :canonical="route('recursos.show', $resource)">
    <main class="mx-auto max-w-4xl px-6 py-12 lg:px-8 lg:py-16">
        <a href="{{ route('recursos') }}" class="text-sm font-semibold text-[#110090] dark:text-[#c1c1ff]">← Volver a recursos</a>
        <article class="mt-8">
            <div class="flex items-start gap-5">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-[#110090] font-display text-xl font-bold text-[#f4bf27]">
                    @if ($resource->logo_url)<img src="{{ $resource->logo_url }}" alt="" class="h-full w-full object-contain p-2">@else{{ str($resource->name)->substr(0, 2)->upper() }}@endif
                </div>
                <div><p class="text-sm font-bold uppercase tracking-wider text-[#f4bf27]">{{ $resource->category->name }}</p><h1 class="mt-1 font-display text-4xl font-bold tracking-tight" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">{{ $resource->name }}</h1></div>
            </div>
            <p class="mt-7 text-xl leading-8" :class="isDark ? 'text-[#b8b8ca]' : 'text-[#4a4a6a]'">{{ $resource->summary }}</p>
            @if ($resource->is_stale)<p class="mt-6 rounded-xl border border-amber-400/50 bg-amber-400/10 p-4 text-sm text-amber-800 dark:text-amber-200">Este recurso no se revisa desde hace más de 90 días. Confirma las condiciones actuales antes de usarlo.</p>@endif
            <div class="mt-8 flex flex-wrap gap-2"><span class="rounded-full bg-[#110090]/10 px-3 py-1.5 text-sm font-semibold text-[#110090] dark:bg-[#c1c1ff]/15 dark:text-[#c1c1ff]">{{ $resource->pricing->label() }}</span>@if ($resource->no_card_required)<span class="rounded-full bg-emerald-500/10 px-3 py-1.5 text-sm font-semibold text-emerald-700 dark:text-emerald-300">No requiere tarjeta</span>@endif @foreach ($resource->technologies as $technology)<span class="rounded-full px-3 py-1.5 text-sm" :class="isDark ? 'bg-[#292934] text-[#9999b3]' : 'bg-gray-100 text-gray-600'">{{ $technology->name }}</span>@endforeach</div>
            <div class="mt-10 grid gap-5 md:grid-cols-2"><section class="rounded-2xl border p-6" :class="isDark ? 'border-[#3a3a55] bg-[#1b1b25]' : 'border-gray-200 bg-white'"><h2 class="font-display text-xl font-bold" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">¿Para qué sirve?</h2><p class="mt-3 leading-7" :class="isDark ? 'text-[#b8b8ca]' : 'text-[#4a4a6a]'">{{ $resource->why_use_it }}</p></section><section class="rounded-2xl border p-6" :class="isDark ? 'border-[#3a3a55] bg-[#1b1b25]' : 'border-gray-200 bg-white'"><h2 class="font-display text-xl font-bold" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">Cuándo no conviene</h2><p class="mt-3 leading-7" :class="isDark ? 'text-[#b8b8ca]' : 'text-[#4a4a6a]'">{{ $resource->when_not_to_use_it }}</p></section></div>
            <section class="mt-5 rounded-2xl border p-6" :class="isDark ? 'border-[#3a3a55] bg-[#1b1b25]' : 'border-gray-200 bg-white'"><h2 class="font-display text-xl font-bold" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">Qué incluye el plan {{ $resource->pricing->label() }}</h2><p class="mt-3 leading-7" :class="isDark ? 'text-[#b8b8ca]' : 'text-[#4a4a6a]'">{{ $resource->free_tier_details }}</p></section>
            <div class="mt-8 flex flex-wrap items-center justify-between gap-4"><p class="text-sm" :class="isDark ? 'text-[#9999b3]' : 'text-gray-500'">Verificado {{ $resource->last_verified_at?->translatedFormat('d \d\e F \d\e Y') ?? 'pendiente' }}</p><a href="{{ $resource->external_url }}" target="_blank" rel="nofollow noopener noreferrer" class="rounded-xl bg-[#f4bf27] px-5 py-3 text-sm font-bold text-[#342600]">Visitar sitio oficial <span aria-hidden="true">↗</span></a></div>
        </article>
    </main>
    <script type="application/ld+json">{!! $structuredData !!}</script>
</x-layouts.public>
