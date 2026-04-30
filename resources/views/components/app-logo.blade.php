@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Mundo Futuro" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-9 items-center justify-center rounded-xl border border-[#110090]/10 bg-white/90 p-1.5 shadow-lg shadow-[#110090]/10 dark:border-[#3a3a55] dark:bg-[#1b1b25]/90 dark:shadow-black/30">
            <img src="{{ asset('assets/img/logo.svg') }}" alt="Mundo Futuro" class="h-full w-full object-contain dark:hidden">
            <img src="{{ asset('assets/img/logo-dark.svg') }}" alt="Mundo Futuro" class="hidden h-full w-full object-contain dark:block">
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Mundo Futuro" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-9 items-center justify-center rounded-xl border border-[#110090]/10 bg-white/90 p-1.5 shadow-lg shadow-[#110090]/10 dark:border-[#3a3a55] dark:bg-[#1b1b25]/90 dark:shadow-black/30">
            <img src="{{ asset('assets/img/logo.svg') }}" alt="Mundo Futuro" class="h-full w-full object-contain dark:hidden">
            <img src="{{ asset('assets/img/logo-dark.svg') }}" alt="Mundo Futuro" class="hidden h-full w-full object-contain dark:block">
        </x-slot>
    </flux:brand>
@endif
