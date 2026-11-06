@extends('layouts.admin')

@section('content')
<div class="mb-8 flex flex-wrap items-end justify-between gap-5">
    <div>
        <div class="flex items-center gap-2 mb-2">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-forest/10 text-forest">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Page Manager
            </span>
            <span class="text-xs text-slate-400">/</span>
            <span class="text-xs uppercase tracking-wider text-forest/70 font-semibold">Homepage</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-serif text-forest">Home Page Content Studio</h1>
        <p class="mt-2 text-sm text-slate-500 max-w-2xl leading-relaxed">
            All sections and content shown on the public homepage are organized here. Click any section below to update texts, media, schedules, or visibility.
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-forest/20 bg-white text-forest text-sm font-medium hover:bg-forest hover:text-white transition-all shadow-xs">
            <span>Preview Public Homepage</span>
            <span class="text-gold">↗</span>
        </a>
    </div>
</div>

@if(session('status'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
        <span>✓</span>
        <span>{{ session('status') }}</span>
    </div>
@endif

<!-- Section Overview Grid -->
<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">

    <!-- 1. Hero Banner Section -->
    <div class="bg-white border border-forest/15 rounded-2xl p-6 shadow-xs flex flex-col justify-between hover:border-gold hover:shadow-md transition-all group">
        <div>
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-xl bg-forest/5 text-forest flex items-center justify-center text-xl font-serif">
                    01
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ ($hero && $hero->is_active) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ ($hero && $hero->is_active) ? 'Active on Homepage' : 'Hidden / Fallback' }}
                </span>
            </div>
            <h2 class="mt-4 font-serif text-xl text-forest group-hover:text-gold transition-colors">Hero & Main Banner</h2>
            <p class="mt-1 text-xs uppercase tracking-wider text-slate-400 font-medium">Top of Homepage</p>
            <p class="mt-3 text-sm text-slate-600 line-clamp-2 leading-relaxed">
                {{ $hero ? ($hero->title ?? 'Hero Title') : 'Original default hero banner title' }}
            </p>
            @if($hero && $hero->subtitle)
                <p class="mt-2 text-xs text-slate-500 italic">“{{ \Illuminate\Support\Str::limit($hero->subtitle, 60) }}”</p>
            @endif
        </div>
        <div class="mt-6 pt-5 border-t border-forest/10 flex items-center justify-between">
            @if($hero)
                <a href="{{ route('admin.homepage.edit', $hero) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                    <span>Edit Hero Banner</span>
                    <span>→</span>
                </a>
            @else
                <a href="{{ route('admin.homepage.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                    <span>Configure Hero</span>
                    <span>→</span>
                </a>
            @endif
            <a href="{{ route('home') }}#hero" target="_blank" class="text-xs text-slate-400 hover:text-forest">View live ↗</a>
        </div>
    </div>

    <!-- 2. Career Highlights / Numbers -->
    <div class="bg-white border border-forest/15 rounded-2xl p-6 shadow-xs flex flex-col justify-between hover:border-gold hover:shadow-md transition-all group">
        <div>
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-xl bg-forest/5 text-forest flex items-center justify-center text-xl font-serif">
                    02
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ ($highlights && $highlights->is_active) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ ($highlights && $highlights->is_active) ? 'Active on Homepage' : 'Hidden / Fallback' }}
                </span>
            </div>
            <h2 class="mt-4 font-serif text-xl text-forest group-hover:text-gold transition-colors">Career Highlights & Stats</h2>
            <p class="mt-1 text-xs uppercase tracking-wider text-slate-400 font-medium">Quick Numbers Grid</p>
            <p class="mt-3 text-sm text-slate-600 line-clamp-2 leading-relaxed">
                {{ $highlights ? ($highlights->title ?? 'Highlights Title') : 'Career Highlights headline and key metrics.' }}
            </p>
            <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                <span class="px-2 py-0.5 rounded bg-forest/5">Key Numbers</span>
                <span class="px-2 py-0.5 rounded bg-forest/5">Tour Milestones</span>
            </div>
        </div>
        <div class="mt-6 pt-5 border-t border-forest/10 flex items-center justify-between">
            @if($highlights)
                <a href="{{ route('admin.homepage.edit', $highlights) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                    <span>Edit Highlights</span>
                    <span>→</span>
                </a>
            @else
                <a href="{{ route('admin.homepage.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                    <span>Configure Stats</span>
                    <span>→</span>
                </a>
            @endif
            <a href="{{ route('home') }}#highlights" target="_blank" class="text-xs text-slate-400 hover:text-forest">View live ↗</a>
        </div>
    </div>

    <!-- 3. Tournament Schedule -->
    <div class="bg-white border border-forest/15 rounded-2xl p-6 shadow-xs flex flex-col justify-between hover:border-gold hover:shadow-md transition-all group">
        <div>
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-xl bg-forest/5 text-forest flex items-center justify-center text-xl font-serif">
                    03
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-forest/10 text-forest">
                    {{ $tournamentsCount }} Total Tournaments
                </span>
            </div>
            <h2 class="mt-4 font-serif text-xl text-forest group-hover:text-gold transition-colors">Tournament Schedule</h2>
            <p class="mt-1 text-xs uppercase tracking-wider text-slate-400 font-medium">Upcoming & Recent Events</p>
            <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                Manages all golf tours, upcoming match dates, venues, status, and purse displayed on the homepage schedule table.
            </p>
            <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 font-medium">{{ $upcomingTournaments }} Upcoming</span>
            </div>
        </div>
        <div class="mt-6 pt-5 border-t border-forest/10 flex items-center justify-between gap-2">
            <a href="{{ route('admin.tournaments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                <span>Manage Tournaments</span>
                <span>→</span>
            </a>
            <a href="{{ route('admin.tournaments.create') }}" class="text-xs font-medium px-2.5 py-1 rounded bg-forest text-white hover:bg-gold hover:text-forest transition-colors">+ Add New</a>
        </div>
    </div>

    <!-- 4. Coaching & Pro Tips -->
    <div class="bg-white border border-forest/15 rounded-2xl p-6 shadow-xs flex flex-col justify-between hover:border-gold hover:shadow-md transition-all group">
        <div>
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-xl bg-forest/5 text-forest flex items-center justify-center text-xl font-serif">
                    04
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-forest/10 text-forest">
                    {{ $coachingCount }} Active Tips
                </span>
            </div>
            <h2 class="mt-4 font-serif text-xl text-forest group-hover:text-gold transition-colors">Coaching & Pro Tips</h2>
            <p class="mt-1 text-xs uppercase tracking-wider text-slate-400 font-medium">Golf Lessons & Advice</p>
            <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                Add and manage professional golf swing techniques, putting guides, mental tips, and drills shown in the home tips section.
            </p>
        </div>
        <div class="mt-6 pt-5 border-t border-forest/10 flex items-center justify-between gap-2">
            <a href="{{ route('admin.coaching.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                <span>Manage Tips</span>
                <span>→</span>
            </a>
            <a href="{{ route('admin.coaching.create') }}" class="text-xs font-medium px-2.5 py-1 rounded bg-forest text-white hover:bg-gold hover:text-forest transition-colors">+ Add Tip</a>
        </div>
    </div>

    <!-- 5. Photo & Video Gallery -->
    <div class="bg-white border border-forest/15 rounded-2xl p-6 shadow-xs flex flex-col justify-between hover:border-gold hover:shadow-md transition-all group">
        <div>
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-xl bg-forest/5 text-forest flex items-center justify-center text-xl font-serif">
                    05
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-forest/10 text-forest">
                    {{ $photosCount }} Photos · {{ $videosCount }} Videos
                </span>
            </div>
            <h2 class="mt-4 font-serif text-xl text-forest group-hover:text-gold transition-colors">Gallery Media</h2>
            <p class="mt-1 text-xs uppercase tracking-wider text-slate-400 font-medium">Photos, Albums & Highlights</p>
            <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                Upload tournament photos, manage photo albums, and embed high-definition YouTube video highlights for the media section.
            </p>
        </div>
        <div class="mt-6 pt-5 border-t border-forest/10 flex items-center justify-between gap-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.photos.index') }}" class="text-sm font-semibold text-forest hover:text-gold transition-colors">Photos ({{ $photosCount }}) →</a>
                <a href="{{ route('admin.videos.index') }}" class="text-sm font-semibold text-forest hover:text-gold transition-colors">Videos ({{ $videosCount }}) →</a>
            </div>
            <a href="{{ route('admin.photo-albums.index') }}" class="text-xs text-slate-400 hover:text-forest">Albums ↗</a>
        </div>
    </div>

    <!-- 6. Beyond the Green / Community -->
    <div class="bg-white border border-forest/15 rounded-2xl p-6 shadow-xs flex flex-col justify-between hover:border-gold hover:shadow-md transition-all group">
        <div>
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-xl bg-forest/5 text-forest flex items-center justify-center text-xl font-serif">
                    06
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ ($beyond && $beyond->is_active) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ ($beyond && $beyond->is_active) ? 'Active on Homepage' : 'Hidden / Fallback' }}
                </span>
            </div>
            <h2 class="mt-4 font-serif text-xl text-forest group-hover:text-gold transition-colors">Beyond the Green</h2>
            <p class="mt-1 text-xs uppercase tracking-wider text-slate-400 font-medium">Community Impact & Values</p>
            <p class="mt-3 text-sm text-slate-600 line-clamp-2 leading-relaxed">
                {{ $beyond ? ($beyond->title ?? 'Beyond Title') : 'Social initiatives and community commitment on the homepage.' }}
            </p>
        </div>
        <div class="mt-6 pt-5 border-t border-forest/10 flex items-center justify-between">
            @if($beyond)
                <a href="{{ route('admin.homepage.edit', $beyond) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                    <span>Edit Impact Section</span>
                    <span>→</span>
                </a>
            @else
                <a href="{{ route('admin.homepage.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                    <span>Configure Beyond</span>
                    <span>→</span>
                </a>
            @endif
            <a href="{{ route('home') }}#beyond" target="_blank" class="text-xs text-slate-400 hover:text-forest">View live ↗</a>
        </div>
    </div>

    <!-- 7. Sponsors & Partners -->
    <div class="bg-white border border-forest/15 rounded-2xl p-6 shadow-xs flex flex-col justify-between hover:border-gold hover:shadow-md transition-all group md:col-span-2 xl:col-span-3">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-forest/5 text-forest flex items-center justify-center text-base font-serif">
                        07
                    </div>
                    <div>
                        <h2 class="font-serif text-xl text-forest group-hover:text-gold transition-colors">Sponsors & Official Partners</h2>
                        <p class="text-xs uppercase tracking-wider text-slate-400 font-medium">Partner Logos at Bottom of Homepage</p>
                    </div>
                </div>
                <p class="mt-3 text-sm text-slate-600 max-w-3xl leading-relaxed">
                    Brand logos, sponsor tiers, and external partner website URLs that show on the homepage partner carousel/grid.
                </p>
            </div>
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-forest/10 text-forest">
                    {{ $sponsorsCount }} Active Sponsors
                </span>
                <a href="{{ route('admin.sponsors.create') }}" class="text-xs font-medium px-3 py-1.5 rounded bg-forest text-white hover:bg-gold hover:text-forest transition-colors">+ Add Sponsor</a>
                <a href="{{ route('admin.sponsors.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                    <span>Manage All Sponsors</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

