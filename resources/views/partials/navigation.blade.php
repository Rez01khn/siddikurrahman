@php
    $theme = $theme ?? config('theme');
    $navItems = $theme['navigation'];
    $socials = array_merge($theme['socials'], $siteSettings?->social_links ?? []);
@endphp
<header id="site-header" data-solid-header="false" class="fixed inset-x-0 top-0 z-50 text-white transition-all duration-300 bg-gradient-to-b from-forest/85 via-forest/45 to-transparent">
    <div class="max-w-[1320px] mx-auto px-5 sm:px-8 lg:px-12 flex h-20 items-center justify-between">
        {{-- Left: SIDDIKUR'S WORLD Logo with Golfer Swing Icon --}}
        <a href="{{ url('/') }}" aria-label="Siddikur's World Home" class="flex items-center gap-2.5 group shrink-0">
            @if($siteSettings?->imageUrl('logo'))
                <img src="{{ $siteSettings->imageUrl('logo') }}" alt="{{ $siteSettings->site_name ?: "Siddikur's World" }}" class="h-11 w-auto object-contain">
            @else
                <span class="relative flex items-center justify-center w-9 h-10 text-white">
                    <svg viewBox="0 0 44 48" fill="none" class="w-9 h-10" aria-hidden="true">
                        <path d="M12 10C19 3 31 3 38 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <ellipse cx="39" cy="12" rx="2.2" ry="1.3" transform="rotate(35 39 12)" fill="currentColor"/>
                        <circle cx="21" cy="13.5" r="3" fill="currentColor"/>
                        <path d="M14 9.5L18.5 16.5L25 18.5L22.5 29L18 41L15.5 41L18.2 28.5L15.5 28.5L12 40.5L9.5 40L14 26.5L15.5 17.5L11.5 12L14 9.5Z" fill="currentColor"/>
                        <path d="M4 39C12 44 27 43 39 34" stroke="#C5A47E" stroke-width="1.4" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="flex flex-col leading-none">
                    <span class="font-serif text-[15px] sm:text-[16px] font-semibold tracking-[0.07em] text-white uppercase">SIDDIKUR’S</span>
                    <span class="text-[8px] font-sans uppercase tracking-[0.38em] text-white/75 mt-1">WORLD</span>
                </span>
            @endif
        </a>

        {{-- Center: Single-Page Navigation Links --}}
        <nav aria-label="Main navigation" class="hidden md:flex items-center gap-7 lg:gap-8 text-[13px] font-normal tracking-wide">
            @foreach($navItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    class="{{ $item['active'] ? 'text-white font-medium relative py-1 after:content-[\'\'] after:absolute after:left-0 after:right-0 after:bottom-0 after:h-[1.5px] after:bg-white' : 'text-white/85 hover:text-gold transition py-1' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Right: Social Circle Icons (Facebook, YouTube, LinkedIn) --}}
        <div class="hidden md:flex items-center gap-2.5">
            <a href="{{ $socials['facebook'] }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="w-6 h-6 rounded-full bg-white text-forest hover:bg-gold hover:text-white flex items-center justify-center transition">
                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M13.5 22v-8.2h2.8l.4-3.2h-3.2V8.5c0-.9.3-1.6 1.6-1.6h1.7V4.1c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.3H7.3v3.2h2.8V22h3.4z"/></svg>
            </a>
            <a href="{{ $socials['youtube'] }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="w-6 h-6 rounded-full bg-white text-forest hover:bg-gold hover:text-white flex items-center justify-center transition">
                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M21.6 7.2a2.7 2.7 0 0 0-1.9-1.9C18 5 12 5 12 5s-6 0-7.7.3a2.7 2.7 0 0 0-1.9 1.9C2 8.9 2 12 2 12s0 3.1.4 4.8a2.7 2.7 0 0 0 1.9 1.9c1.7.3 7.7.3 7.7.3s6 0 7.7-.3a2.7 2.7 0 0 0 1.9-1.9c.4-1.7.4-4.8.4-4.8s0-3.1-.4-4.8zM10 15V9l5.2 3-5.2 3z"/></svg>
            </a>
            <a href="{{ $socials['linkedin'] }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="w-6 h-6 rounded-full bg-white text-forest hover:bg-gold hover:text-white flex items-center justify-center transition">
                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M6.9 8.5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM5.2 10h3.4v10.8H5.2V10zm5.5 0h3.2v1.5h.1c.5-.8 1.6-1.8 3.3-1.8 3.5 0 4.2 2.3 4.2 5.3v5.8H18v-5.1c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7v5.2h-3.6V10z"/></svg>
            </a>
        </div>

        {{-- Mobile Hamburger Button --}}
        <button id="menu-toggle" class="md:hidden border border-white/35 rounded-full px-3.5 py-1.5 text-xs text-white flex items-center gap-2" aria-controls="mobile-menu" aria-expanded="false">
            <span>Menu</span>
            <span aria-hidden="true">☰</span>
        </button>
    </div>

    {{-- Mobile Navigation Drawer --}}
    <nav id="mobile-menu" aria-label="Mobile navigation" hidden class="bg-forest px-6 py-6 space-y-3 border-b border-gold/30 shadow-2xl">
        @foreach($navItems as $item)
            <a class="block py-1.5 text-sm {{ $item['active'] ? 'font-medium text-white' : 'text-white/85' }}" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
        @endforeach
    </nav>
</header>
