@props(['currentRoute' => null])

<header class="sticky top-0 z-50 border-b transition-colors duration-300"
    :class="isDark
        ?
        'bg-[#2e2e3d]/80 border-[#3a3a55]/30 backdrop-blur-xl' :
        'bg-white/80 border-gray-200/50 backdrop-blur-xl'"
    x-data="{ open: false }">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-3 lg:px-8">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3" aria-label="Mundo Futuro">
            <img :src="isDark ? '{{ asset('assets/img/logo-dark.svg') }}' : '{{ asset('assets/img/logo.svg') }}'" alt="Mundo Futuro" class="h-9 w-auto">
            <span class="font-display hidden text-base font-bold tracking-tight sm:block"
                :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                MUNDO FUTURO
            </span>
        </a>

        {{-- Desktop links --}}
        <ul class="hidden items-center gap-1 lg:flex">
            @php
                $links = [
                    ['label' => 'Inicio', 'route' => 'home', 'disabled' => false],
                    ['label' => 'Publicaciones', 'route' => 'publicaciones', 'disabled' => false],
                    ['label' => 'Tutoriales', 'route' => 'tutoriales', 'disabled' => false],
                    ['label' => 'Cursos', 'route' => null, 'disabled' => true],
                    ['label' => 'Comunidad', 'route' => null, 'disabled' => true],
                ];
            @endphp

            @foreach ($links as $link)
                <li>
                    @if ($link['disabled'])
                        <span class="cursor-not-allowed px-4 py-2 text-sm font-medium opacity-40 transition-opacity"
                            :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'" title="Próximamente">
                            {{ $link['label'] }}
                        </span>
                    @else
                        <a href="{{ route($link['route']) }}"
                            class="rounded-md px-4 py-2 text-sm font-medium transition-colors"
                            :class="isDark
                                ?
                                'text-[#e2e2f0] hover:text-[#f4bf27]' :
                                'text-[#12121d] hover:text-[#110090]'">
                            {{ $link['label'] }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>

        {{-- Right actions --}}
        <div class="flex items-center gap-3">

            {{-- Theme toggle --}}
            <button @click="toggle()" class="rounded-lg p-2 transition-colors"
                :class="isDark
                    ?
                    'text-[#9999b3] hover:text-[#e2e2f0] hover:bg-[#292934]' :
                    'text-gray-500 hover:text-gray-900 hover:bg-gray-100'"
                :aria-label="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'">
                {{-- Sun icon (show in dark mode to switch to light) --}}
                <svg x-show="isDark" style="display:none" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>
                {{-- Moon icon (show in light mode to switch to dark) --}}
                <svg x-show="!isDark" style="display:none" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                </svg>
            </button>

            {{-- CTA button (desktop) --}}
            {{-- <a

                href="{{ route('register') }}"
                class="hidden rounded-lg bg-[#f4bf27] px-4 py-2 text-sm font-bold text-[#342600] transition-all hover:brightness-110 sm:block"
            >
                COMENZAR →
            </a> --}}

            {{-- Mobile hamburger --}}
            <button @click="open = !open" class="rounded-lg p-2 transition-colors lg:hidden"
                :class="isDark
                    ?
                    'text-[#9999b3] hover:text-[#e2e2f0] hover:bg-[#292934]' :
                    'text-gray-500 hover:text-gray-900 hover:bg-gray-100'"
                aria-label="Menú">
                <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </nav>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" class="border-t px-6 pb-4 lg:hidden"
        :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200/50'">
        <ul class="mt-3 flex flex-col gap-1">
            <li>
                <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 text-sm font-medium transition-colors"
                    :class="isDark ? 'text-[#e2e2f0] hover:text-[#f4bf27] hover:bg-[#292934]' :
                        'text-[#12121d] hover:bg-gray-100'">
                    Inicio
                </a>
            </li>
            @foreach (['Cursos', 'Comunidad'] as $item)
                <li>
                    <span class="block cursor-not-allowed rounded-md px-3 py-2 text-sm font-medium opacity-40"
                        :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'">
                        {{ $item }}
                    </span>
                </li>
            @endforeach
        </ul>
        <div class="mt-4 border-t pt-4" :class="isDark ? 'border-[#3a3a55]/30' : 'border-gray-200/50'">
            <a href="{{ route('register') }}"
                class="block w-full rounded-lg bg-[#f4bf27] py-2 text-center text-sm font-bold text-[#342600]">
                COMENZAR →
            </a>
        </div>
    </div>
</header>
