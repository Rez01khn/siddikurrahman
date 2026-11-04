@props(['title'])
<header class="sticky top-0 z-30 bg-white border-b border-forest/10 px-5 sm:px-8 lg:px-10 h-20 flex items-center justify-between gap-4">
    <div class="flex items-center gap-4 min-w-0"><button id="sidebar-toggle" class="lg:hidden border border-forest/20 rounded-lg p-2.5" aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open navigation">☰</button><div><p class="hidden sm:block text-[10px] uppercase tracking-[.16em] text-slate-500">Your website workspace</p><p class="text-sm font-semibold text-forest">{{ $title }}</p></div></div>
    <details id="profile-menu" class="relative">
        <summary class="flex items-center gap-3 cursor-pointer list-none rounded-lg p-2 hover:bg-ivory"><span class="w-9 h-9 rounded-full bg-forest text-gold flex items-center justify-center font-semibold">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span class="hidden sm:block text-sm max-w-40 truncate">{{ auth()->user()->name }}</span><span aria-hidden="true">⌄</span></summary>
        <div class="absolute right-0 mt-3 w-64 max-w-[85vw] bg-white rounded-xl border border-forest/15 shadow-lg p-2"><div class="p-3 border-b border-forest/10 mb-2"><p class="text-sm font-semibold break-words">{{ auth()->user()->name }}</p><p class="text-xs text-slate-500 break-all mt-1">{{ auth()->user()->email }}</p><p class="text-[10px] uppercase tracking-widest text-forest mt-3">Administrator</p></div><a href="{{ route('admin.users.edit',auth()->user()) }}" class="block px-3 py-2 text-sm text-forest">Manage my account</a><x-admin.logout /></div>
    </details>
</header>
