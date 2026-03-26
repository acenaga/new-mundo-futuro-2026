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
                init() {
                    // Use same key as Flux so theme stays in sync across public/app
                    const stored = localStorage.getItem('flux_appearance') ?? localStorage.getItem('appearance');
                    this.isDark = stored !== 'light';
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
