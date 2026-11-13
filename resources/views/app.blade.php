<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $title = 'CornerTill';
        $description = 'Simplify your sales process and boost your productivity.';
        $url = config('app.url', 'http://localhost:8000');
        $mediumLogo = '/assets/logo/cornertill-logo.png';

    @endphp

    <!-- General -->
    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <title inertia>{{ config('app.name', $title) }}</title>
    <meta name="title" content="{{ $title }}">
    <meta name="description" content="{{ $description }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $url }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" itemprop="image" content="{{ $mediumLogo }}">

    <!-- Twitter/X -->
    <meta property="twitter:card" content="summary">
    <meta property="twitter:url" content="{{ $url }}">
    <meta property="twitter:title" content="{{ $title }}">
    <meta property="twitter:description" content="{{ $description }}">
    <meta property="twitter:image" content="{{ $mediumLogo }}">
    <meta name="twitter:site" content="@Ahmad_Alkaf_">

    <!-- Fonts: Figtree is bundled by Vite (resources/js/app.tsx); no font CDN. -->

    <!-- Scripts -->
    @routes
    {{-- No @viteReactRefresh: vite.config.js has no React plugin, so its
         /@react-refresh script does not exist (a 404 with `npm run dev`).
         Production output is the same: the directive prints nothing without
         a running dev server. --}}
    @vite(['resources/js/app.tsx', "resources/js/Pages/{$page['component']}.tsx"])

    <!-- Styles -->
    @inertiaHead

    {{-- Cashier Paddle (only when PAYMENTS_ENABLED=true) --}}
    @if (config('features.payments_enabled'))
        @paddleJS
    @endif
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
