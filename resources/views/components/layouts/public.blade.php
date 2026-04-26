@props(['title' => null, 'description' => null, 'ogImage' => null, 'ogType' => 'website', 'canonical' => null])

<!DOCTYPE html>
<html
    lang="es"
    x-data="themeManager()"
    x-init="init()"
    :class="isDark ? 'dark' : ''"
    class="scroll-smooth"
>
<head>
    @include('partials.head')
    @stack('head')
</head>
<body class="public-body antialiased transition-colors duration-300">
    <x-public.navbar />

    <main>
        {{ $slot }}
    </main>

    <x-public.footer />

    @fluxScripts

    <script>
        function themeManager() {
            return {
                isDark: true,
                mediaQuery: window.matchMedia('(prefers-color-scheme: dark)'),
                syncFromStorage() {
                    const stored = localStorage.getItem('flux_appearance') ?? localStorage.getItem('appearance');

                    this.isDark = window.resolveThemePreference(stored) === 'dark';
                },
                handleSystemAppearanceChange(event) {
                    const stored = localStorage.getItem('flux_appearance') ?? localStorage.getItem('appearance');

                    if (stored === 'system') {
                        this.isDark = event.matches;
                    }
                },
                init() {
                    this.syncFromStorage();

                    document.documentElement.classList.toggle('dark', this.isDark);

                    this.$watch('isDark', val => {
                        document.documentElement.classList.toggle('dark', val);
                    });

                    this.handleSystemAppearanceChange = this.handleSystemAppearanceChange.bind(this);
                    this.mediaQuery.addEventListener('change', this.handleSystemAppearanceChange);
                },
                toggle() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('flux_appearance', this.isDark ? 'dark' : 'light');
                },
            };
        }
    </script>
</body>
</html>
