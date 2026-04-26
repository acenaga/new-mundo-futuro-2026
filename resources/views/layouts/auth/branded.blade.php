@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head')
    </head>
    <body
        x-data="authTheme()"
        x-init="init()"
        class="min-h-screen bg-[#f4f4fb] text-[#12121d] antialiased transition-colors duration-300 dark:bg-[#12121d] dark:text-[#e2e2f0]"
    >
        <div class="relative isolate min-h-svh overflow-hidden">
            <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
                <div class="absolute -left-24 top-0 h-72 w-72 rounded-full bg-[#c1c1ff]/45 blur-3xl dark:bg-[#4c2e84]/25"></div>
                <div class="absolute right-0 top-1/3 h-80 w-80 rounded-full bg-[#d3bbff]/35 blur-3xl dark:bg-[#110090]/25"></div>
                <div class="absolute bottom-0 left-1/3 h-64 w-64 rounded-full bg-[#f4bf27]/15 blur-3xl dark:bg-[#f4bf27]/10"></div>
            </div>

            <div class="relative mx-auto grid min-h-svh w-full max-w-7xl lg:grid-cols-[minmax(0,1fr)_minmax(28rem,32rem)]">
                <aside class="hidden flex-col justify-between gap-10 px-8 py-10 lg:flex xl:px-12">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3 self-start" wire:navigate>
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl border border-[#110090]/10 bg-white/90 p-2 shadow-lg shadow-[#110090]/10 dark:border-[#3a3a55] dark:bg-[#1b1b25]/90 dark:shadow-black/30">
                            <img
                                :src="isDark ? '{{ asset('assets/img/logo-dark.svg') }}' : '{{ asset('assets/img/logo.svg') }}'"
                                alt="Mundo Futuro"
                                class="h-full w-full object-contain"
                            >
                        </span>

                        <span class="flex flex-col">
                            <span class="font-display text-lg font-bold tracking-tight text-[#12121d] dark:text-[#e2e2f0]">Mundo Futuro</span>
                            <span class="text-sm text-[#4a4a6a] dark:text-[#9999b3]">Tecnologia, aprendizaje y comunidad</span>
                        </span>
                    </a>

                    <div class="max-w-xl space-y-6">
                        <div class="flex items-center gap-2">
                            <div class="h-px w-8 bg-[#110090] dark:bg-[#f4bf27]"></div>
                            <span class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">
                                Acceso a la plataforma
                            </span>
                        </div>

                        <div class="space-y-4">
                            <h1 class="font-display text-4xl font-bold tracking-tight text-[#12121d] dark:text-[#e2e2f0] xl:text-5xl">
                                Aprende hoy. Construye el futuro.
                            </h1>

                            <p class="max-w-lg text-base leading-relaxed text-[#4a4a6a] dark:text-[#9999b3]">
                                Ingresa a tus cursos, tutoriales y publicaciones favoritas con la identidad visual de Mundo Futuro.
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-[#110090]/10 bg-white/70 p-4 backdrop-blur dark:border-[#3a3a55] dark:bg-[#1b1b25]/80">
                                <div class="font-display text-sm font-semibold text-[#110090] dark:text-[#f4bf27]">Cursos</div>
                                <div class="mt-1 text-sm text-[#4a4a6a] dark:text-[#9999b3]">Ruta guiada de aprendizaje</div>
                            </div>

                            <div class="rounded-2xl border border-[#110090]/10 bg-white/70 p-4 backdrop-blur dark:border-[#3a3a55] dark:bg-[#1b1b25]/80">
                                <div class="font-display text-sm font-semibold text-[#110090] dark:text-[#f4bf27]">Tutoriales</div>
                                <div class="mt-1 text-sm text-[#4a4a6a] dark:text-[#9999b3]">Practica paso a paso</div>
                            </div>

                            <div class="rounded-2xl border border-[#110090]/10 bg-white/70 p-4 backdrop-blur dark:border-[#3a3a55] dark:bg-[#1b1b25]/80">
                                <div class="font-display text-sm font-semibold text-[#110090] dark:text-[#f4bf27]">Noticias</div>
                                <div class="mt-1 text-sm text-[#4a4a6a] dark:text-[#9999b3]">Actualidad del mundo tech</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-[#110090]/10 bg-white/65 p-6 backdrop-blur dark:border-[#3a3a55] dark:bg-[#1b1b25]/75">
                        <p class="font-display text-sm font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">
                            Tema sincronizado
                        </p>
                        <p class="mt-2 text-sm leading-6 text-[#4a4a6a] dark:text-[#9999b3]">
                            Estas pantallas respetan la preferencia claro u oscuro que elegiste desde el home.
                        </p>
                    </div>
                </aside>

                <main class="flex px-4 py-4 sm:px-6 sm:py-6 md:px-8 md:py-8 lg:items-center lg:justify-center lg:px-10 lg:py-10">
                    <div class="w-full max-w-xl lg:max-w-lg">
                        <div class="mb-4 flex items-center justify-between gap-3 sm:mb-6">
                            <button
                                type="button"
                                @click="goBackOrHome()"
                                class="inline-flex items-center gap-2 rounded-full border border-[#110090]/10 bg-white/85 px-3 py-2 text-sm font-semibold text-[#12121d] shadow-sm shadow-[#110090]/5 transition hover:border-[#110090]/20 hover:bg-white dark:border-[#3a3a55] dark:bg-[#1b1b25]/85 dark:text-[#e2e2f0] dark:hover:border-[#f4bf27]/30 dark:hover:bg-[#21212d]"
                                data-test="auth-back-button"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                </svg>
                                <span>Volver</span>
                            </button>

                            <a href="{{ route('home') }}" class="inline-flex items-center gap-3 lg:hidden" wire:navigate>
                                <span class="flex h-11 w-11 items-center justify-center rounded-2xl border border-[#110090]/10 bg-white/90 p-2 shadow-lg shadow-[#110090]/10 dark:border-[#3a3a55] dark:bg-[#1b1b25]/90 dark:shadow-black/30 sm:h-12 sm:w-12">
                                    <img
                                        :src="isDark ? '{{ asset('assets/img/logo-dark.svg') }}' : '{{ asset('assets/img/logo.svg') }}'"
                                        alt="Mundo Futuro"
                                        class="h-full w-full object-contain"
                                    >
                                </span>

                                <span class="hidden flex-col text-end sm:flex">
                                    <span class="font-display text-sm font-bold tracking-tight text-[#12121d] dark:text-[#e2e2f0]">Mundo Futuro</span>
                                    <span class="text-xs text-[#4a4a6a] dark:text-[#9999b3]">Volver al inicio</span>
                                </span>
                            </a>
                        </div>

                        <div class="mb-4 rounded-[1.75rem] border border-[#110090]/10 bg-white/75 p-4 shadow-xl shadow-[#110090]/10 backdrop-blur dark:border-[#3a3a55] dark:bg-[#1b1b25]/80 dark:shadow-black/30 sm:mb-6 sm:p-5 lg:hidden">
                            <div class="flex items-center gap-2">
                                <div class="h-px w-8 bg-[#110090] dark:bg-[#f4bf27]"></div>
                                <span class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">
                                    Acceso a la plataforma
                                </span>
                            </div>

                            <div class="mt-3 space-y-3">
                                <div>
                                    <h1 class="font-display text-2xl font-bold tracking-tight text-[#12121d] dark:text-[#e2e2f0] sm:text-3xl">
                                        Aprende hoy. Construye el futuro.
                                    </h1>
                                    <p class="mt-2 text-sm leading-6 text-[#4a4a6a] dark:text-[#9999b3]">
                                        Accede a cursos, tutoriales y noticias con la preferencia de tema que elegiste desde el home.
                                    </p>
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div class="rounded-2xl border border-[#110090]/10 bg-white/80 px-3 py-3 text-center text-xs font-semibold text-[#110090] dark:border-[#3a3a55] dark:bg-[#21212d] dark:text-[#f4bf27]">
                                        Cursos
                                    </div>
                                    <div class="rounded-2xl border border-[#110090]/10 bg-white/80 px-3 py-3 text-center text-xs font-semibold text-[#110090] dark:border-[#3a3a55] dark:bg-[#21212d] dark:text-[#f4bf27]">
                                        Tutoriales
                                    </div>
                                    <div class="rounded-2xl border border-[#110090]/10 bg-white/80 px-3 py-3 text-center text-xs font-semibold text-[#110090] dark:border-[#3a3a55] dark:bg-[#21212d] dark:text-[#f4bf27]">
                                        Noticias
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-[1.75rem] border border-[#110090]/10 bg-white/80 shadow-2xl shadow-[#110090]/10 backdrop-blur-xl dark:border-[#3a3a55] dark:bg-[#1b1b25]/85 dark:shadow-black/40 sm:rounded-[2rem]">
                            <div class="border-b border-[#110090]/10 px-5 py-4 sm:px-6 sm:py-5 lg:px-8 dark:border-[#3a3a55]">
                                <div class="flex items-start gap-3 sm:items-center sm:gap-4">
                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#e8e8ff] p-2 dark:bg-[#21212d] sm:h-14 sm:w-14">
                                        <img
                                            :src="isDark ? '{{ asset('assets/img/logo-dark.svg') }}' : '{{ asset('assets/img/logo.svg') }}'"
                                            alt="Mundo Futuro"
                                            class="h-full w-full object-contain"
                                        >
                                    </span>

                                    <div class="min-w-0">
                                        <p class="font-display text-base font-bold tracking-tight text-[#12121d] dark:text-[#e2e2f0] sm:text-lg">Mundo Futuro</p>
                                        <p class="text-sm leading-6 text-[#4a4a6a] dark:text-[#9999b3]">Accede con tu cuenta o crea una nueva para continuar.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="px-5 py-6 sm:px-6 sm:py-8 lg:px-8">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <script>
            function authTheme() {
                return {
                    isDark: window.resolveThemePreference(window.getStoredAppearance()) === 'dark',
                    mediaQuery: window.matchMedia('(prefers-color-scheme: dark)'),
                    syncAppearance() {
                        this.isDark = window.resolveThemePreference(window.getStoredAppearance()) === 'dark';

                        document.documentElement.classList.toggle('dark', this.isDark);
                    },
                    handleSystemAppearanceChange(event) {
                        if (window.getStoredAppearance() === 'system') {
                            this.isDark = event.matches;
                            document.documentElement.classList.toggle('dark', this.isDark);
                        }
                    },
                    init() {
                        this.syncAppearance();

                        this.handleStorageChange = () => this.syncAppearance();
                        window.addEventListener('storage', this.handleStorageChange);

                        this.handleSystemAppearanceChange = this.handleSystemAppearanceChange.bind(this);
                        this.mediaQuery.addEventListener('change', this.handleSystemAppearanceChange);
                    },
                    goBackOrHome() {
                        if (window.history.length > 1) {
                            window.history.back();

                            return;
                        }

                        window.location.assign('{{ route('home') }}');
                    },
                };
            }
        </script>
        @fluxScripts
    </body>
</html>
