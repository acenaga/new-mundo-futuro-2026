<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#f4f4fb] text-[#12121d] dark:bg-[#12121d] dark:text-[#e2e2f0]">
        <flux:header container class="border-b border-[#110090]/10 bg-white/80 backdrop-blur dark:border-[#3a3a55] dark:bg-[#12121d]/85">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>
                <flux:navbar.item icon="newspaper" :href="route('publicaciones')" :current="request()->routeIs('publicaciones*')" wire:navigate>
                    {{ __('Publicaciones') }}
                </flux:navbar.item>
                <flux:navbar.item icon="academic-cap" :href="route('tutoriales')" :current="request()->routeIs('tutoriales*')" wire:navigate>
                    {{ __('Tutoriales') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('Volver al inicio')" position="bottom">
                    <flux:navbar.item
                        class="h-10 [&>div>svg]:size-5"
                        icon="arrow-up-right"
                        :href="route('home')"
                        :label="__('Inicio')"
                        wire:navigate
                    />
                </flux:tooltip>
            </flux:navbar>

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-[#110090]/10 bg-white/90 dark:border-[#3a3a55] dark:bg-[#12121d]">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Plataforma')">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard')  }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="newspaper" :href="route('publicaciones')" :current="request()->routeIs('publicaciones*')" wire:navigate>
                        {{ __('Publicaciones') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="academic-cap" :href="route('tutoriales')" :current="request()->routeIs('tutoriales*')" wire:navigate>
                        {{ __('Tutoriales') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="arrow-up-right" :href="route('home')" wire:navigate>
                    {{ __('Inicio') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
