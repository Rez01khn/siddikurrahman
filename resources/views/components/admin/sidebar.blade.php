<aside id="admin-sidebar" aria-label="Administration navigation" class="fixed inset-y-0 left-0 z-50 w-72 bg-forest text-white flex flex-col -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-2xl lg:shadow-none">
    <!-- Brand / Logo -->
    <div class="flex items-center justify-between px-5 py-5 border-b border-white/15">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
            <span class="text-3xl font-serif text-gold group-hover:scale-105 transition-transform">SR.</span>
            <span class="text-sm leading-tight font-semibold">Siddikur Rahman
                <span class="block text-[10px] uppercase tracking-[.2em] text-white/60 font-normal">Content Studio</span>
            </span>
        </a>
        
        <!-- Sidebar OFF / ON Button (Top Right of Sidebar) -->
        <button id="sidebar-off-btn" type="button" class="group flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border border-white/20 bg-white/10 hover:bg-white/20 text-white transition-all shadow-xs cursor-pointer select-none" title="Turn Sidebar OFF">
            <span class="w-2 h-2 rounded-full bg-emerald-400 group-hover:bg-rose-400 transition-colors"></span>
            <span class="text-[10px] font-bold tracking-wider uppercase">ON</span>
        </button>
        <button id="sidebar-close" class="hidden" aria-label="Close navigation"></button>
    </div>

    <!-- Navigation Scroll Area -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-6" aria-label="Admin modules">
        
        <!-- Dashboard Link -->
        <div>
            <a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gold text-forest font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Section: Page Content Management -->
        <div class="space-y-3">
            <p class="px-3 text-[10px] uppercase tracking-[.2em] text-white/50 font-semibold">Pages Content</p>

            <!-- Homepage Group -->
            @php
                $isHomeActive = request()->routeIs([
                    'admin.pages.home',
                    'admin.homepage.*',
                    'admin.achievements.*',
                    'admin.tournaments.*',
                    'admin.coaching.*',
                    'admin.photos.*',
                    'admin.photo-albums.*',
                    'admin.videos.*',
                    'admin.sponsors.*',
                ]);
            @endphp
            <details class="group rounded-xl overflow-hidden {{ $isHomeActive ? 'bg-white/5' : '' }}" {{ $isHomeActive || !request()->routeIs('admin.pages.about', 'admin.biography.*') ? 'open' : '' }}>
                <summary class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold cursor-pointer select-none text-white hover:bg-white/10 transition-colors list-none">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span>Homepage</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-white/60 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                
                <div class="pt-1 pb-2 pl-4 pr-2 space-y-0.5 border-l-2 border-gold/40 ml-4 my-1">
                    <a href="{{ route('admin.pages.home') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.pages.home') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>◈ Overview & Sections</span>
                    </a>
                    <a href="{{ route('admin.homepage.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.homepage.*') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>• Hero Section</span>
                    </a>
                    <a href="{{ route('admin.achievements.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.achievements.*') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>• Key Achievements</span>
                    </a>
                    <a href="{{ route('admin.tournaments.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.tournaments.*') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>• Tournament Schedule</span>
                    </a>
                    <a href="{{ route('admin.coaching.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.coaching.*') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>• Coaching & Tips</span>
                    </a>
                    <a href="{{ route('admin.photos.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.photos.*', 'admin.photo-albums.*') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>• Photo Gallery</span>
                    </a>
                    <a href="{{ route('admin.videos.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.videos.*') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>• Video Gallery</span>
                    </a>
                    <a href="{{ route('admin.sponsors.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.sponsors.*') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>• Sponsors & Partners</span>
                    </a>
                </div>
            </details>

            <!-- About Page Group -->
            @php
                $isAboutActive = request()->routeIs([
                    'admin.pages.about',
                    'admin.biography.*',
                    'admin.achievements.*',
                ]);
            @endphp
            <details class="group rounded-xl overflow-hidden {{ $isAboutActive ? 'bg-white/5' : '' }}" {{ $isAboutActive ? 'open' : '' }}>
                <summary class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold cursor-pointer select-none text-white hover:bg-white/10 transition-colors list-none">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>About Page</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-white/60 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                
                <div class="pt-1 pb-2 pl-4 pr-2 space-y-0.5 border-l-2 border-gold/40 ml-4 my-1">
                    <a href="{{ route('admin.pages.about') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.pages.about') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>◈ Overview & Sections</span>
                    </a>
                    <a href="{{ route('admin.biography.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.biography.*') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>• Biography & Story</span>
                    </a>
                    <a href="{{ route('admin.achievements.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors {{ request()->routeIs('admin.achievements.*') ? 'bg-gold text-forest font-semibold' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                        <span>• Key Achievements</span>
                    </a>
                </div>
            </details>

        </div>

        <!-- Section: General Settings -->
        <div class="space-y-1">
            <p class="px-3 text-[10px] uppercase tracking-[.2em] text-white/50 font-semibold">Settings & System</p>
            
            <a href="{{ route('admin.settings.edit') }}" class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-gold text-forest font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Website Settings</span>
                </div>
            </a>

            <a href="{{ route('admin.messages.index') }}" class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.messages.*') ? 'bg-gold text-forest font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Contact Messages</span>
                </div>
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-gold text-forest font-semibold shadow-xs' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Admin Users</span>
                </div>
            </a>
        </div>

    </nav>
</aside>
