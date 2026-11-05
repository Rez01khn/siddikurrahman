<footer class="relative bg-forest-deep text-white border-t border-white/10 overflow-hidden">
    {{-- Subtle right corner wave lines matching reference footer --}}
    <svg class="pointer-events-none absolute right-0 bottom-0 h-16 w-48 text-white/10" viewBox="0 0 200 70" fill="none" aria-hidden="true">
        <path d="M20 70C65 25 135 15 200 35" stroke="currentColor" stroke-width="1"/>
        <path d="M55 70C95 38 150 28 200 45" stroke="currentColor" stroke-width="1"/>
    </svg>

    <div class="max-w-[1320px] mx-auto px-5 sm:px-8 lg:px-12 py-5 flex flex-col sm:flex-row items-center justify-between gap-4 relative z-10">
        {{-- Left: SIDDIKUR'S WORLD Logo --}}
        <a href="#home-hero" class="flex items-center gap-2.5 shrink-0">
            <span class="relative flex items-center justify-center w-8 h-9 text-white">
                <svg viewBox="0 0 44 48" fill="none" class="w-8 h-9" aria-hidden="true">
                    <path d="M12 10C19 3 31 3 38 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <ellipse cx="39" cy="12" rx="2.2" ry="1.3" transform="rotate(35 39 12)" fill="currentColor"/>
                    <circle cx="21" cy="13.5" r="3" fill="currentColor"/>
                    <path d="M14 9.5L18.5 16.5L25 18.5L22.5 29L18 41L15.5 41L18.2 28.5L15.5 28.5L12 40.5L9.5 40L14 26.5L15.5 17.5L11.5 12L14 9.5Z" fill="currentColor"/>
                    <path d="M4 39C12 44 27 43 39 34" stroke="#C5A47E" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="flex flex-col leading-none">
                <span class="font-serif text-[14px] font-semibold tracking-[0.07em] text-white uppercase">{{ $siteSettings?->site_name ?: "SIDDIKUR’S" }}</span>
                <span class="text-[7.5px] font-sans uppercase tracking-[0.38em] text-white/70 mt-1">WORLD</span>
            </span>
        </a>

        {{-- Center: Copyright --}}
        <p class="text-[11.5px] text-white/70 tracking-wide text-center">
            © 2025 Siddikur’s World. All Rights Reserved.
        </p>

        {{-- Right: Location "| Bangladesh" --}}
        <div class="flex items-center gap-2.5 text-[11.5px] text-white/80">
            <span class="text-white/30">|</span>
            <span class="inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.2-6-10a6 6 0 1 1 12 0c0 4.8-6 10-6 10z"/>
                    <circle cx="12" cy="11" r="2"/>
                </svg>
                <span>Bangladesh</span>
            </span>
        </div>
    </div>
</footer>
