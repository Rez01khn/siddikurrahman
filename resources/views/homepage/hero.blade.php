<section id="top" class="relative min-h-[740px] h-[100svh] max-h-[1200px] bg-forest text-white flex items-center">
    <img src="{{ $section->imageUrl() }}" alt="{{ $section->text('image_alt') }}" class="absolute inset-0 h-full w-full object-cover object-[65%_center]" fetchpriority="high">
    <div class="absolute inset-0 hero-shade"></div>
    <div class="shell relative pt-24 pb-32">
        <p class="eyebrow mb-7 flex items-center gap-3"><span class="h-2 w-2 rounded-full bg-gold"></span> {{ $section->text('eyebrow') }}</p>
        <h1 class="font-display uppercase text-[clamp(5.2rem,10vw,10rem)] leading-[.85] tracking-[-.015em]"><x-homepage.lines :text="$section->text('title')" accent-last /></h1>
        <div class="mt-9 md:ml-1 max-w-sm"><p class="text-base text-white/80 leading-7"><x-homepage.lines :text="$section->text('description')" /></p><a href="{{ $section->buttonUrl($visibleAnchors) }}" class="mt-8 inline-flex items-center gap-10 bg-gold text-forest px-6 py-4 text-sm font-bold">{{ $section->text('button_text') }} <span aria-hidden="true">↗</span></a></div>
    </div>
    <div class="absolute bottom-0 inset-x-0"><div class="shell border-t border-white/25 py-6 flex flex-wrap md:flex-nowrap justify-between items-center gap-6"><span class="text-sm uppercase tracking-[.16em]">{{ $section->text('footer_label') }}</span><span class="w-full md:w-auto order-3 md:order-none text-xs md:text-sm text-white/70">{{ $section->text('image_note') }}</span><a href="{{ in_array('introduction', $visibleAnchors, true) ? '#introduction' : '#main' }}" class="text-sm">{{ $section->text('explore_label') }} <span class="ml-3">↓</span></a></div></div>
</section>
