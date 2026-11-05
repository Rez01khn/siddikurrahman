@php
    $theme = $theme ?? config('theme');
    $colors = $theme['colors'];
    $fonts = $theme['fonts'];
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo')
    @if($siteSettings?->imageUrl('favicon'))<link rel="icon" type="image/png" href="{{ $siteSettings->imageUrl('favicon') }}">@endif
    <meta name="theme-color" content="{{ $colors['forest'] }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $fonts['google_fonts_url'] }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        if (window.tailwind) {
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            forest: '{{ $colors['forest'] }}',
                            'forest-deep': '{{ $colors['forest_deep'] }}',
                            'forest-panel': '{{ $colors['forest_panel'] }}',
                            'forest-gallery': '{{ $colors['forest_gallery'] }}',
                            'forest-accent': '{{ $colors['accent_green'] }}',
                            'forest-btn': '{{ $colors['dark_btn'] }}',
                            cream: '{{ $colors['cream'] }}',
                            'cream-about': '{{ $colors['cream_about'] }}',
                            'cream-banner': '{{ $colors['cream_banner'] }}',
                            gold: '{{ $colors['gold'] }}',
                            charcoal: '{{ $colors['charcoal'] }}',
                            ivory: '{{ $colors['cream'] }}',
                            ink: '{{ $colors['charcoal'] }}',
                        },
                        fontFamily: {
                            sans: @json($fonts['body']),
                            serif: @json($fonts['heading']),
                            display: @json($fonts['heading']),
                            signature: @json($fonts['signature']),
                        }
                    }
                }
            };
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v=2.1">
    <script src="{{ asset('js/site.js') }}?v=2.1" defer></script>
</head>
<body class="bg-cream text-charcoal font-sans antialiased selection:bg-gold selection:text-forest">
    <a href="#main" class="skip-link">Skip to content</a>
    @include('partials.navigation')
    <main id="main">@yield('content')</main>
    @include('partials.footer')
    <dialog id="media-dialog" aria-labelledby="media-title" class="bg-cream text-forest p-0 w-[min(92vw,1000px)] rounded-md shadow-2xl">
        <div class="flex items-center justify-between gap-4 p-5 border-b border-forest/15">
            <h2 id="media-title" class="text-sm font-medium">Moments on the Course</h2>
            <button data-close-dialog class="px-3 py-1.5 text-xs border border-forest/30 rounded-full hover:bg-forest hover:text-white transition" aria-label="Close image">Close ×</button>
        </div>
        <img id="media-image" alt="" class="w-full max-h-[75vh] object-contain bg-black/95">
        <p id="media-status" role="status" class="p-5 text-sm" hidden></p>
    </dialog>
</body>
</html>
