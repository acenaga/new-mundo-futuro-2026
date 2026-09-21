<x-layouts.public title="Recursos para desarrolladores — {{ config('app.name') }}"
    description="Herramientas gratuitas y freemium seleccionadas para desarrollar mejor, con límites explicados y revisión editorial."
    :canonical="route('recursos')">
    <main class="mx-auto max-w-7xl px-6 py-12 lg:px-8 lg:py-16">
        <section class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#f4bf27]">Recursos para desarrolladores</p>
            <h1 class="mt-3 font-display text-4xl font-bold tracking-tight sm:text-5xl" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">Construye más. Paga menos.</h1>
            <p class="mt-5 text-lg leading-8" :class="isDark ? 'text-[#b8b8ca]' : 'text-[#4a4a6a]'">Una selección editorial de herramientas gratuitas y freemium. Te contamos para qué sirven, sus límites y cuándo quizá conviene mirar otra opción.</p>
        </section>

        <form method="GET" class="mt-10 grid gap-3 rounded-2xl border p-4 md:grid-cols-5" :class="isDark ? 'border-[#3a3a55] bg-[#1b1b25]' : 'border-gray-200 bg-white'">
            <label class="md:col-span-2">
                <span class="sr-only">Buscar recurso</span>
                <input name="q" value="{{ $filters['search'] }}" placeholder="Buscar por nombre o utilidad" class="w-full rounded-xl border px-4 py-2.5 text-sm" :class="isDark ? 'border-[#3a3a55] bg-[#292934] text-[#e2e2f0]' : 'border-gray-200 bg-white text-[#12121d]'">
            </label>
            <select name="categoria" class="rounded-xl border px-3 py-2.5 text-sm" :class="isDark ? 'border-[#3a3a55] bg-[#292934] text-[#e2e2f0]' : 'border-gray-200 bg-white'">
                <option value="">Todas las categorías</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected($filters['categorySlug'] === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="tecnologia" class="rounded-xl border px-3 py-2.5 text-sm" :class="isDark ? 'border-[#3a3a55] bg-[#292934] text-[#e2e2f0]' : 'border-gray-200 bg-white'">
                <option value="">Toda tecnología</option>
                @foreach ($technologies as $technology)
                    <option value="{{ $technology->slug }}" @selected($filters['technologySlug'] === $technology->slug)>{{ $technology->name }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 text-sm" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                    <input type="checkbox" name="sin_tarjeta" value="1" @checked($filters['withoutCard']) class="rounded border-gray-300 text-[#110090] focus:ring-[#110090]"> Sin tarjeta
                </label>
                <button type="submit" class="rounded-xl bg-[#f4bf27] px-4 py-2.5 text-sm font-bold text-[#342600]">Filtrar</button>
            </div>
        </form>

        @if ($featured->isNotEmpty() && ! $filters['search'] && ! $filters['categorySlug'] && ! $filters['technologySlug'] && ! $filters['pricing'] && ! $filters['withoutCard'])
            <section class="mt-14">
                <div class="flex items-end justify-between gap-4"><h2 class="font-display text-2xl font-bold" :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">Selección editorial</h2><span class="text-sm" :class="isDark ? 'text-[#9999b3]' : 'text-gray-500'">Elegidos por utilidad y claridad de su plan</span></div>
                <div class="mt-6 grid gap-5 md:grid-cols-3">@foreach ($featured as $resource)<x-recursos.card :resource="$resource" />@endforeach</div>
            </section>
        @endif

        <section class="mt-14">
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
