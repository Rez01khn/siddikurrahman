<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo')
    @if($siteSettings?->imageUrl('favicon'))<link rel="icon" type="image/png" href="{{ $siteSettings->imageUrl('favicon') }}">@endif
    <meta name="theme-color" content="#12372B">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>if (window.tailwind) { tailwind.config = {theme: {extend: {colors: {forest: '#12372B', gold: '#C6A667', ivory: '#F5F3EC', ink: '#1B1B1B'}, fontFamily: {sans: ['DM Sans', 'sans-serif'], display: ['Barlow Condensed', 'sans-serif']}}}}; }</script>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v=1.1">
    <script src="{{ asset('js/site.js') }}?v=1.1" defer></script>
</head>
<body class="bg-ivory text-ink antialiased">
    <a href="#main" class="skip-link">Skip to content</a>
    @include('partials.navigation')
    <main id="main">@yield('content')</main>
    @include('partials.footer')
    <dialog id="media-dialog" aria-labelledby="media-title" class="bg-ivory text-forest p-0 w-[min(92vw,1000px)]">
        <div class="flex items-center justify-between gap-4 p-5"><h2 id="media-title" class="text-sm">Golf photography · stock image</h2><button data-close-dialog class="p-2 border border-forest/30" aria-label="Close image">Close ×</button></div>
        <img id="media-image" alt="" class="w-full max-h-[75vh] object-contain">
        <p id="media-status" role="status" class="p-5 text-sm" hidden></p>
    </dialog>
</body>
</html>
