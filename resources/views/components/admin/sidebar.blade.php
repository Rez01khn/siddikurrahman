<aside id="admin-sidebar" aria-label="Administration navigation" class="fixed inset-y-0 left-0 z-50 w-72 bg-forest text-white flex flex-col -translate-x-full lg:translate-x-0 transition-transform duration-200">
    <div class="flex items-center justify-between px-6 py-7 border-b border-white/15">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3"><span class="text-4xl font-serif text-gold">SR.</span><span class="text-sm leading-5 font-semibold">Siddikur Rahman<span class="block text-[10px] uppercase tracking-[.2em] text-white/60">Editorial studio</span></span></a>
        <button id="sidebar-close" class="lg:hidden p-2" aria-label="Close navigation">✕</button>
    </div>
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1" aria-label="Admin modules">
        <a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10' }}"><span aria-hidden="true">◈</span> Dashboard</a>
        <p class="px-3 pt-6 pb-3 text-[10px] uppercase tracking-[.18em] text-white/50">Website modules · Phase 3</p>
        @foreach(config('admin.modules') as $slug => $label)
            @php
                $live = in_array($slug, ['legacy-imports', 'homepage', 'biography', 'milestones', 'achievements', 'statistics', 'tournaments', 'tournament-results', 'news', 'news-categories','photos','photo-albums','videos','sponsors','social-works','equipment','coaching','messages','settings','users'], true);
                $active = $live ? request()->routeIs('admin.'.$slug.'.*') : request()->route('module') === $slug;
                $link = $live ? route('admin.'.$slug.'.'.(in_array($slug,['biography','settings'],true) ? 'edit' : 'index')) : route('admin.modules.show', $slug);
            @endphp
            <a href="{{ $link }}" @if($active) aria-current="page" @endif class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm {{ $active ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10' }}"><span>{{ $label }}</span><span class="text-[9px] border border-current/20 rounded px-1.5 py-0.5 opacity-70">{{ $live ? 'Live' : 'Soon' }}</span></a>
        @endforeach
    </nav>
    <div class="px-6 py-5 border-t border-white/15"><a class="text-sm text-gold hover:text-white" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">View public website ↗</a></div>
</aside>
