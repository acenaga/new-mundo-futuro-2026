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
            const stored = localStorage.getItem('flux_appearance') ?? localStorage.getItem('appearance');
            return {
                isDark: stored !== 'light',
                init() {
                    // Apply initial state — :class on <html> root is unreliable in Alpine
                    document.documentElement.classList.toggle('dark', this.isDark);
                    this.$watch('isDark', val => {
                        document.documentElement.classList.toggle('dark', val);
                        localStorage.setItem('flux_appearance', val ? 'dark' : 'light');
                    });
                },
                toggle() {
                    this.isDark = !this.isDark;
                },
            };
        }
    </script>
</body>
</html>
