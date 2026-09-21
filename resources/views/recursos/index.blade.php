<x-layouts.public title="Recursos para desarrolladores — {{ config('app.name') }}"
    description="Herramientas gratuitas y freemium seleccionadas para desarrollar mejor, con límites explicados y revisión editorial."
    :canonical="route('recursos')">
    @php
        $hasActiveFilters = $filters['search'] || $filters['categorySlug'] || $filters['technologySlug'] || $filters['pricing'] || $filters['withoutCard'];
    @endphp

    <main class="mx-auto max-w-7xl px-6 py-10 lg:px-8 lg:py-14">
        <section class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#f4bf27]">Recursos para desarrolladores</p>
            <h1 class="mt-3 font-display text-4xl font-bold tracking-tight sm:text-5xl" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">Construye más. Paga menos.</h1>
            <p class="mt-5 text-lg leading-8" :class="isDark ? 'text-[#b8b8ca]' : 'text-[#4a4a6a]'">Una selección editorial de herramientas gratuitas y freemium. Te contamos para qué sirven, sus límites y cuándo quizá conviene mirar otra opción.</p>
        </section>

        <form method="GET" class="mt-8 grid gap-3 rounded-2xl border p-4 md:grid-cols-2 xl:grid-cols-6" :class="isDark ? 'border-[#3a3a55] bg-[#1b1b25]' : 'border-gray-200 bg-white'">
            <label class="xl:col-span-2">
                <span class="sr-only">Buscar recurso</span>
                <input name="q" value="{{ $filters['search'] }}" placeholder="Buscar por nombre o utilidad" class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none ring-2 ring-transparent transition focus:ring-[#f4bf27]" :class="isDark ? 'border-[#3a3a55] bg-[#292934] text-[#e2e2f0] placeholder:text-[#9999b3]' : 'border-gray-200 bg-white text-[#12121d] placeholder:text-gray-400'">
            </label>
            <select name="categoria" class="rounded-xl border px-3 py-2.5 text-sm outline-none ring-2 ring-transparent transition focus:ring-[#f4bf27]" :class="isDark ? 'border-[#3a3a55] bg-[#292934] text-[#e2e2f0]' : 'border-gray-200 bg-white text-[#12121d]'">
                <option value="">Todas las categorías</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected($filters['categorySlug'] === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="tecnologia" class="rounded-xl border px-3 py-2.5 text-sm outline-none ring-2 ring-transparent transition focus:ring-[#f4bf27]" :class="isDark ? 'border-[#3a3a55] bg-[#292934] text-[#e2e2f0]' : 'border-gray-200 bg-white text-[#12121d]'">
                <option value="">Toda tecnología</option>
                @foreach ($technologies as $technology)
                    <option value="{{ $technology->slug }}" @selected($filters['technologySlug'] === $technology->slug)>{{ $technology->name }}</option>
                @endforeach
            </select>
            <select name="modalidad" class="rounded-xl border px-3 py-2.5 text-sm outline-none ring-2 ring-transparent transition focus:ring-[#f4bf27]" :class="isDark ? 'border-[#3a3a55] bg-[#292934] text-[#e2e2f0]' : 'border-gray-200 bg-white text-[#12121d]'">
                <option value="">Gratis o freemium</option>
                <option value="free" @selected($filters['pricing'] === 'free')>Gratis</option>
                <option value="freemium" @selected($filters['pricing'] === 'freemium')>Freemium</option>
            </select>
            <div class="flex flex-wrap items-center gap-3">
                <label class="flex items-center gap-2 text-sm" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                    <input type="checkbox" name="sin_tarjeta" value="1" @checked($filters['withoutCard']) class="rounded border-gray-300 text-[#110090] focus:ring-[#110090]"> Sin tarjeta
                </label>
                <button type="submit" class="rounded-xl bg-[#f4bf27] px-4 py-2.5 text-sm font-bold text-[#342600] transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-[#f4bf27] focus:ring-offset-2" :class="isDark ? 'focus:ring-offset-[#1b1b25]' : 'focus:ring-offset-white'">Filtrar</button>
            </div>
        </form>

        @if ($hasActiveFilters)
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl px-1 text-sm" :class="isDark ? 'text-[#b8b8ca]' : 'text-[#4a4a6a]'">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-semibold" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">Filtros activos:</span>
                    @if ($filters['search'])<span class="rounded-full px-3 py-1" :class="isDark ? 'bg-[#292934]' : 'bg-gray-100'">“{{ $filters['search'] }}”</span>@endif
                    @if ($filters['categorySlug'])<span class="rounded-full px-3 py-1" :class="isDark ? 'bg-[#292934]' : 'bg-gray-100'">{{ $categories->firstWhere('slug', $filters['categorySlug'])?->name }}</span>@endif
                    @if ($filters['technologySlug'])<span class="rounded-full px-3 py-1" :class="isDark ? 'bg-[#292934]' : 'bg-gray-100'">{{ $technologies->firstWhere('slug', $filters['technologySlug'])?->name }}</span>@endif
                    @if ($filters['pricing'])<span class="rounded-full px-3 py-1" :class="isDark ? 'bg-[#292934]' : 'bg-gray-100'">{{ $filters['pricing'] === 'free' ? 'Gratis' : 'Freemium' }}</span>@endif
                    @if ($filters['withoutCard'])<span class="rounded-full px-3 py-1" :class="isDark ? 'bg-[#292934]' : 'bg-gray-100'">Sin tarjeta</span>@endif
                </div>
                <a href="{{ route('recursos') }}" class="font-semibold text-[#110090] underline-offset-4 hover:underline focus:outline-none focus:ring-2 focus:ring-[#f4bf27] dark:text-[#c1c1ff]">Limpiar filtros</a>
            </div>
        @endif

        @if ($featured->isNotEmpty() && ! $filters['search'] && ! $filters['categorySlug'] && ! $filters['technologySlug'] && ! $filters['pricing'] && ! $filters['withoutCard'])
            <section class="mt-12">
                <div class="flex items-end justify-between gap-4"><h2 class="font-display text-2xl font-bold" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">Selección editorial</h2><span class="text-sm" :class="isDark ? 'text-[#9999b3]' : 'text-gray-500'">Elegidos por utilidad y claridad de su plan</span></div>
                <div class="mt-6 grid gap-5 md:grid-cols-3">@foreach ($featured as $resource)<x-recursos.card :resource="$resource" />@endforeach</div>
            </section>
        @endif

        <section class="mt-12">
            <div class="flex items-end justify-between gap-4"><h2 class="font-display text-2xl font-bold" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">Explora el catálogo</h2><span class="text-sm" :class="isDark ? 'text-[#9999b3]' : 'text-gray-500'">{{ $resources->total() }} recursos</span></div>
            @if ($resources->isNotEmpty())
                <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">@foreach ($resources as $resource)<x-recursos.card :resource="$resource" />@endforeach</div>
                <div class="mt-10">{{ $resources->links() }}</div>
            @else
                <div class="mt-6 rounded-2xl border border-dashed p-10 text-center" :class="isDark ? 'border-[#3a3a55] text-[#9999b3]' : 'border-gray-300 text-gray-500'">No encontramos recursos con esos filtros.</div>
            @endif
        </section>
    </main>
</x-layouts.public>
