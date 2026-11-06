@props(['title' => ''])
<header class="sticky top-0 z-30 bg-white border-b border-forest/10 px-5 sm:px-8 lg:px-10 h-16 sm:h-20 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <button id="sidebar-toggle" class="border border-forest/20 rounded-xl px-3 py-1.5 text-xs font-semibold text-forest hover:bg-forest hover:text-white transition-all flex items-center gap-2 shadow-xs cursor-pointer" aria-controls="admin-sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <span id="topbar-sidebar-label" class="text-xs font-semibold">Sidebar: <span id="topbar-sidebar-status">ON</span></span>
        </button>
    </div>

    <details id="profile-menu" class="relative group">
        <summary class="flex items-center gap-2.5 cursor-pointer list-none [&::-webkit-details-marker]:hidden rounded-full border border-forest/15 py-1.5 pl-1.5 pr-3.5 bg-white hover:bg-ivory/70 hover:border-gold transition-all shadow-xs select-none">
            <span class="w-8 h-8 rounded-full bg-forest text-gold flex items-center justify-center font-bold text-xs uppercase tracking-wider shrink-0">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </span>
            <span class="text-xs sm:text-sm font-semibold text-forest leading-none">
                {{ auth()->user()->name }}
            </span>
            <svg class="w-3.5 h-3.5 text-slate-400 group-open:rotate-180 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </summary>
        <div class="absolute right-0 mt-2 w-64 max-w-[85vw] bg-white rounded-xl border border-forest/15 shadow-xl p-2 z-50">
            <div class="p-3 border-b border-forest/10 mb-1">
                <p class="text-sm font-semibold text-forest break-words">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-500 break-all mt-0.5">{{ auth()->user()->email }}</p>
                <span class="inline-block text-[10px] uppercase tracking-wider bg-forest/10 text-forest font-semibold px-2 py-0.5 rounded mt-2">Administrator</span>
            </div>
            <a href="{{ route('admin.users.edit', auth()->user()) }}" class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-700 hover:text-forest hover:bg-ivory rounded-lg transition-colors">
                <span>Manage my account</span>
            </a>
            <div class="border-t border-forest/10 mt-1 pt-1">
                <x-admin.logout />
            </div>
        </div>
    </details>
</header>
