<footer
    class="mt-20 border-t transition-colors duration-300"
    :class="isDark ? 'border-[#3a3a55]/30 bg-[#0d0d18]' : 'border-gray-200 bg-white'"
>
    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
        <div class="flex flex-col items-start justify-between gap-8 md:flex-row md:items-center">

            {{-- Brand --}}
            <div class="flex flex-col gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/img/logo-wb.png') }}" alt="Mundo Futuro" class="h-8 w-auto">
                    <span
                        class="font-display text-sm font-bold tracking-tight"
                        :class="isDark ? 'text-[#e2e2f0]' : 'text-[#12121d]'"
                    >
                        MUNDO FUTURO
                    </span>
                </a>
                <p class="max-w-xs text-xs" :class="isDark ? 'text-[#9999b3]' : 'text-gray-500'">
                    &copy; {{ date('Y') }} Mundo Futuro. Al sistema operacional.
                </p>
            </div>

            {{-- Links --}}
            <nav aria-label="Footer">
                <ul class="flex flex-wrap gap-x-6 gap-y-2">
                    @foreach (['Documentación', 'Changelog', 'Política de Privacidad', 'Contacto'] as $item)
                        <li>
                            <a
                                href="#"
                                class="text-xs font-medium uppercase tracking-widest transition-colors"
                                :class="isDark
                                    ? 'text-[#9999b3] hover:text-[#e2e2f0]'
                                    : 'text-gray-500 hover:text-gray-900'"
                            >
                                {{ $item }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- Social icons --}}
            <div class="flex items-center gap-4">
                {{-- Twitter / X --}}
                <a
                    href="#"
                    class="transition-colors"
                    :class="isDark ? 'text-[#9999b3] hover:text-[#f4bf27]' : 'text-gray-400 hover:text-gray-900'"
                    aria-label="Twitter / X"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                {{-- GitHub --}}
                <a
                    href="#"
                    class="transition-colors"
                    :class="isDark ? 'text-[#9999b3] hover:text-[#f4bf27]' : 'text-gray-400 hover:text-gray-900'"
                    aria-label="GitHub"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844a9.59 9.59 0 0 1 2.504.337c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2Z" clip-rule="evenodd"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>
