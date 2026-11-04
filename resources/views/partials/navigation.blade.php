<header id="site-header" data-solid-header="{{ ($solidHeader ?? ($sections->first()?->section_key !== 'hero')) ? 'true' : 'false' }}" class="fixed inset-x-0 top-0 z-40 text-white transition-colors duration-300 {{ ($solidHeader ?? ($sections->first()?->section_key !== 'hero')) ? 'scrolled' : '' }}">
    <div class="shell flex h-24 items-center justify-between border-b border-white/25">
        <a href="{{ route('home') }}" aria-label="Siddikur Rahman home" class="flex items-center gap-3">@if($siteSettings?->imageUrl('logo'))<img src="{{ $siteSettings->imageUrl('logo') }}" alt="{{ $siteSettings->site_name ?: 'Siddikur Rahman' }}" class="w-28 h-16 object-contain">@else<span class="font-display text-5xl tracking-[-.08em] pr-1">SR<span class="text-gold">.</span></span><span class="text-sm uppercase tracking-[.19em] leading-relaxed max-w-36 break-words">@if($siteSettings?->site_name){{ $siteSettings->site_name }}@else Siddikur<br>Rahman @endif</span>@endif</a>
        <nav aria-label="Main navigation" class="hidden xl:flex items-center gap-5 text-sm font-medium">
            <a href="{{ route('biography') }}" @if(request()->routeIs('biography')) aria-current="page" @endif>Biography</a>
            @if(in_array('journey', $visibleAnchors, true))<a href="#journey">The journey</a>@endif
@if(in_array('career', $visibleAnchors, true))<a href="#career">Career</a>@endif
            <a href="{{ route('tournaments.index') }}" @if(request()->routeIs('tournaments.*')) aria-current="page" @endif>On tour</a>
            <a href="{{ route('news.index') }}" @if(request()->routeIs('news.*')) aria-current="page" @endif>Journal</a>
<a href="{{ route('photos.index') }}">Media</a>
<a href="{{ route('social-works.index') }}">Beyond golf</a>
            <details class="relative"><summary class="cursor-pointer">Explore</summary><div class="absolute right-0 mt-5 w-52 bg-forest border border-white/20 p-4 space-y-3 shadow-lg"><a class="block" href="{{ route('sponsors.index') }}">Sponsors</a><a class="block" href="{{ route('in-the-bag') }}">In the bag</a><a class="block" href="{{ route('coaching-tips.index') }}">Coaching tips</a><a class="block" href="{{ route('videos.index') }}">Video gallery</a><a class="block" href="{{ route('contact') }}">Contact</a></div></details>
        </nav>
        @if(in_array('contact', $visibleAnchors, true))<a href="#contact" class="hidden xl:block border border-white/50 px-5 py-3 text-sm">Get in touch <span class="ml-5">↗</span></a>@endif
        <button id="menu-toggle" class="xl:hidden border border-white/40 px-4 py-2 text-sm" aria-controls="mobile-menu" aria-expanded="false">Menu <span aria-hidden="true">☰</span></button>
    </div>
    <nav id="mobile-menu" aria-label="Mobile navigation" hidden class="bg-forest px-6 py-6 space-y-4 border-b border-gold/30">
        <a class="block py-2 text-lg" href="{{ route('biography') }}">Biography</a>
        <a class="block py-2 text-lg" href="{{ route('tournaments.index') }}">On tour</a>
        <a class="block py-2 text-lg" href="{{ route('news.index') }}">Journal</a>
        <a class="block py-2 text-lg" href="{{ route('photos.index') }}">Photo gallery</a><a class="block py-2 text-lg" href="{{ route('videos.index') }}">Video gallery</a>
        <a class="block py-2 text-lg" href="{{ route('sponsors.index') }}">Sponsors</a><a class="block py-2 text-lg" href="{{ route('social-works.index') }}">Social works</a>
        <a class="block py-2 text-lg" href="{{ route('in-the-bag') }}">In the bag</a><a class="block py-2 text-lg" href="{{ route('coaching-tips.index') }}">Coaching tips</a><a class="block py-2 text-lg" href="{{ route('contact') }}">Contact</a>
        @foreach(['journey' => 'The journey', 'career' => 'Career', 'schedule' => 'On tour', 'journal' => 'Journal', 'media' => 'Media', 'community' => 'Beyond golf', 'contact' => 'Get in touch'] as $id => $label)
            @if(!in_array($id, ['schedule','journal','media','community'], true) && in_array($id, $visibleAnchors, true))<a class="block py-2 text-lg" href="#{{ $id }}">{{ $label }}</a>@endif
        @endforeach
    </nav>
</header>
