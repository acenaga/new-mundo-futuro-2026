<meta charset="utf-8" />
<script>
    (function () {
        var s = localStorage.getItem('flux_appearance') || localStorage.getItem('appearance');
        if (s === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    }());
</script>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
@php
    $seoTitle = $title ?? config('app.name');
    $seoDescription = strip_tags($description ?? 'Mundo Futuro es una plataforma educativa enfocada en desarrollo web avanzado, tutoriales prácticos y publicaciones técnicas.');
    $seoCanonical = $canonical ?? url()->current();
    $seoOgType = $ogType ?? 'website';
    $seoOgImage = $ogImage ?? asset('apple-touch-icon.png');
    $seoLocale = str_replace('_', '-', app()->getLocale());
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}" />
<link rel="canonical" href="{{ $seoCanonical }}">

<meta property="og:type" content="{{ $seoOgType }}" />
<meta property="og:title" content="{{ $seoTitle }}" />
<meta property="og:description" content="{{ $seoDescription }}" />
<meta property="og:url" content="{{ $seoCanonical }}" />
<meta property="og:site_name" content="{{ config('app.name') }}" />
<meta property="og:locale" content="{{ $seoLocale }}" />
<meta property="og:image" content="{{ $seoOgImage }}" />
<meta property="og:image:alt" content="{{ $seoTitle }}" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $seoTitle }}" />
<meta name="twitter:description" content="{{ $seoDescription }}" />
<meta name="twitter:image" content="{{ $seoOgImage }}" />

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|space-grotesk:400,500,600,700|inter:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
