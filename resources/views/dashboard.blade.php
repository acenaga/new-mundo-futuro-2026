<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <section class="relative overflow-hidden rounded-[2rem] border border-[#110090]/10 bg-white/80 p-6 shadow-2xl shadow-[#110090]/10 backdrop-blur dark:border-[#3a3a55] dark:bg-[#1b1b25]/85 dark:shadow-black/30 sm:p-8">
            <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
                <div class="absolute -left-20 top-0 h-56 w-56 rounded-full bg-[#c1c1ff]/45 blur-3xl dark:bg-[#4c2e84]/25"></div>
                <div class="absolute right-0 top-8 h-64 w-64 rounded-full bg-[#d3bbff]/35 blur-3xl dark:bg-[#110090]/25"></div>
                <div class="absolute bottom-0 left-1/3 h-48 w-48 rounded-full bg-[#f4bf27]/20 blur-3xl dark:bg-[#f4bf27]/10"></div>
            </div>

            <div class="relative grid gap-8 lg:grid-cols-[minmax(0,1.5fr)_minmax(20rem,1fr)] lg:items-start">
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl border border-[#110090]/10 bg-white/90 p-2 shadow-lg shadow-[#110090]/10 dark:border-[#3a3a55] dark:bg-[#21212d]/90 dark:shadow-black/30">
                            <img src="{{ asset('assets/img/logo.svg') }}" alt="Mundo Futuro" class="h-full w-full object-contain dark:hidden">
                            <img src="{{ asset('assets/img/logo-dark.svg') }}" alt="Mundo Futuro" class="hidden h-full w-full object-contain dark:block">
                        </span>

                        <div>
                            <p class="font-display text-sm font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">
                                Dashboard de aprendizaje
                            </p>
                            <p class="text-sm text-[#4a4a6a] dark:text-[#9999b3]">
                                Hola, {{ auth()->user()->name }}.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h1 class="font-display text-4xl font-bold tracking-tight text-[#12121d] dark:text-[#e2e2f0] sm:text-5xl">
                            Continua tu ruta de aprendizaje en Mundo Futuro.
                        </h1>

                        <p class="max-w-2xl text-base leading-7 text-[#4a4a6a] dark:text-[#9999b3]">
                            Explora tutoriales, publicaciones y recursos pensados para ayudarte a avanzar con claridad, práctica y comunidad.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a
                            href="{{ route('tutoriales') }}"
                            class="font-display inline-flex items-center justify-center rounded-xl bg-[#f4bf27] px-5 py-3 text-sm font-bold text-[#342600] transition hover:brightness-110"
                            wire:navigate
                        >
                            Explorar tutoriales
                        </a>
                        <a
                            href="{{ route('publicaciones') }}"
                            class="font-display inline-flex items-center justify-center rounded-xl border border-[#110090]/10 bg-white/80 px-5 py-3 text-sm font-semibold text-[#12121d] transition hover:border-[#110090]/30 hover:text-[#110090] dark:border-[#3a3a55] dark:bg-[#21212d] dark:text-[#e2e2f0] dark:hover:border-[#f4bf27]/40 dark:hover:text-[#f4bf27]"
                            wire:navigate
                        >
                            Ver publicaciones
                        </a>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="rounded-[1.5rem] border border-[#110090]/10 bg-white/75 p-5 backdrop-blur dark:border-[#3a3a55] dark:bg-[#21212d]/80">
                        <p class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">
                            Tu acceso
                        </p>
                        <div class="mt-3 flex items-center justify-between gap-4">
                            <div>
                                <p class="font-display text-2xl font-bold text-[#12121d] dark:text-[#e2e2f0]">
                                    {{ auth()->user()->hasRole('student') ? 'Student' : 'Miembro' }}
                                </p>
                                <p class="mt-1 text-sm text-[#4a4a6a] dark:text-[#9999b3]">
                                    Tu panel está enfocado en aprendizaje y contenido público autenticado.
                                </p>
                            </div>
                            <span class="rounded-full bg-[#110090] px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-white dark:bg-[#f4bf27] dark:text-[#342600]">
                                Activo
                            </span>
                        </div>
                    </div>

                    <div class="rounded-[1.5rem] border border-[#110090]/10 bg-[#110090] p-5 text-white shadow-lg shadow-[#110090]/20 dark:border-[#1f17b0] dark:bg-[#110090]">
                        <p class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#c1c1ff] dark:text-[#f8df8c]">
                            Proximo paso
                        </p>
                        <p class="mt-3 font-display text-2xl font-bold tracking-tight">
                            Empieza por una guia practica y sigue con lecturas tecnicas.
                        </p>
                        <p class="mt-2 text-sm leading-6 text-[#e8e8ff] dark:text-[#d8d8f5]">
                            La mejor combinacion para avanzar rapido es alternar tutoriales aplicados con publicaciones de referencia.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-4 lg:grid-cols-3">
            <a
                href="{{ route('tutoriales') }}"
                class="group rounded-[1.5rem] border border-[#110090]/10 bg-white/80 p-5 shadow-lg shadow-[#110090]/5 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-[#110090]/10 dark:border-[#3a3a55] dark:bg-[#1b1b25]/85 dark:hover:border-[#f4bf27]/20"
                wire:navigate
            >
                <p class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">Tutoriales</p>
                <h2 class="mt-3 font-display text-xl font-bold tracking-tight text-[#12121d] transition group-hover:text-[#110090] dark:text-[#e2e2f0] dark:group-hover:text-[#f4bf27]">
                    Practica paso a paso
                </h2>
                <p class="mt-2 text-sm leading-6 text-[#4a4a6a] dark:text-[#9999b3]">
                    Recorridos guiados para construir, probar y entender cada concepto.
                </p>
            </a>

            <a
                href="{{ route('publicaciones') }}"
                class="group rounded-[1.5rem] border border-[#110090]/10 bg-white/80 p-5 shadow-lg shadow-[#110090]/5 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-[#110090]/10 dark:border-[#3a3a55] dark:bg-[#1b1b25]/85 dark:hover:border-[#f4bf27]/20"
                wire:navigate
            >
                <p class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">Publicaciones</p>
                <h2 class="mt-3 font-display text-xl font-bold tracking-tight text-[#12121d] transition group-hover:text-[#110090] dark:text-[#e2e2f0] dark:group-hover:text-[#f4bf27]">
                    Lecturas con contexto
                </h2>
                <p class="mt-2 text-sm leading-6 text-[#4a4a6a] dark:text-[#9999b3]">
                    Articulos y piezas tecnicas para reforzar criterio, tendencias y buenas practicas.
                </p>
            </a>

            <a
                href="{{ route('profile.edit') }}"
                class="group rounded-[1.5rem] border border-[#110090]/10 bg-white/80 p-5 shadow-lg shadow-[#110090]/5 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-[#110090]/10 dark:border-[#3a3a55] dark:bg-[#1b1b25]/85 dark:hover:border-[#f4bf27]/20"
                wire:navigate
            >
                <p class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">Perfil</p>
                <h2 class="mt-3 font-display text-xl font-bold tracking-tight text-[#12121d] transition group-hover:text-[#110090] dark:text-[#e2e2f0] dark:group-hover:text-[#f4bf27]">
                    Mantene tu cuenta al dia
                </h2>
                <p class="mt-2 text-sm leading-6 text-[#4a4a6a] dark:text-[#9999b3]">
                    Gestiona tu informacion, seguridad y preferencias desde el area personal.
                </p>
            </a>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
            <div class="rounded-[1.5rem] border border-[#110090]/10 bg-white/80 p-6 shadow-lg shadow-[#110090]/5 dark:border-[#3a3a55] dark:bg-[#1b1b25]/85">
                <p class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">
                    En esta cuenta
                </p>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-[#110090]/10 bg-[#f4f4fb] p-4 dark:border-[#3a3a55] dark:bg-[#21212d]">
                        <p class="text-sm text-[#4a4a6a] dark:text-[#9999b3]">Rol actual</p>
                        <p class="mt-2 font-display text-2xl font-bold text-[#12121d] dark:text-[#e2e2f0]">
                            {{ auth()->user()->getRoleNames()->first() ?? 'Sin rol' }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-[#110090]/10 bg-[#f4f4fb] p-4 dark:border-[#3a3a55] dark:bg-[#21212d]">
                        <p class="text-sm text-[#4a4a6a] dark:text-[#9999b3]">Correo verificado</p>
                        <p class="mt-2 font-display text-2xl font-bold text-[#12121d] dark:text-[#e2e2f0]">
                            {{ auth()->user()->hasVerifiedEmail() ? 'Si' : 'No' }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-[#110090]/10 bg-[#f4f4fb] p-4 dark:border-[#3a3a55] dark:bg-[#21212d]">
                        <p class="text-sm text-[#4a4a6a] dark:text-[#9999b3]">Contenido</p>
                        <p class="mt-2 font-display text-2xl font-bold text-[#12121d] dark:text-[#e2e2f0]">
                            Disponible
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-[1.5rem] border border-[#110090]/10 bg-white/80 p-6 shadow-lg shadow-[#110090]/5 dark:border-[#3a3a55] dark:bg-[#1b1b25]/85">
                <p class="font-display text-xs font-semibold uppercase tracking-[0.18em] text-[#110090] dark:text-[#f4bf27]">
                    Recomendacion
                </p>
                <h2 class="mt-3 font-display text-2xl font-bold tracking-tight text-[#12121d] dark:text-[#e2e2f0]">
                    Reserva este espacio como punto de partida.
                </h2>
                <p class="mt-3 text-sm leading-6 text-[#4a4a6a] dark:text-[#9999b3]">
                    Desde aqui vas a poder entrar rapido a los recursos principales sin exponer el panel administrativo a estudiantes.
                </p>
            </div>
        </div>
    </div>
</x-layouts::app>
