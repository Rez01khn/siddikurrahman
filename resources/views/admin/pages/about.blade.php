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
            <span class="text-xs uppercase tracking-wider text-forest/70 font-semibold">About Page</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-serif text-forest">About Page Content Studio</h1>
        <p class="mt-2 text-sm text-slate-500 max-w-2xl leading-relaxed">
            Manage the complete biography, personal profile, milestones, career story, inspirational quote, and trophy case displayed on the public About page.
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('about') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-forest/20 bg-white text-forest text-sm font-medium hover:bg-forest hover:text-white transition-all shadow-xs">
            <span>Preview Public About Page</span>
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
<div class="grid md:grid-cols-2 gap-6">

    <!-- 1. Biography & Personal Profile -->
    <div class="bg-white border border-forest/15 rounded-2xl p-7 shadow-xs flex flex-col justify-between hover:border-gold hover:shadow-md transition-all group">
        <div>
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-xl bg-forest/5 text-forest flex items-center justify-center text-xl font-serif">
                    01
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    Live on About Page
                </span>
            </div>
            <h2 class="mt-5 font-serif text-2xl text-forest group-hover:text-gold transition-colors">Profile, Bio & Story</h2>
            <p class="mt-1 text-xs uppercase tracking-wider text-slate-400 font-medium">Personal Details & Career Journey</p>
            
            <div class="mt-5 space-y-3 text-sm text-slate-600 bg-ivory/50 p-4 rounded-xl border border-forest/10">
                <div class="flex justify-between items-center py-1 border-b border-forest/10">
                    <span class="text-slate-500">Headline:</span>
                    <span class="font-medium text-forest truncate max-w-xs">{{ $biography ? ($biography->headline ?? 'Pioneering Golfer') : 'Default Headline' }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-forest/10">
                    <span class="text-slate-500">Turned Pro:</span>
                    <span class="font-medium text-forest">{{ $biography && $biography->turned_pro_year ? $biography->turned_pro_year : '2005' }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-500">Quote:</span>
                    <span class="font-medium text-forest italic truncate max-w-xs">“{{ $biography && $biography->quote ? \Illuminate\Support\Str::limit($biography->quote, 45) : 'Inspiring golf quote' }}”</span>
                </div>
            </div>

            <p class="mt-4 text-xs text-slate-500 leading-relaxed">
                Includes biography profile image, hero tagline, birth date, height, residence, detailed career story, philosophy, and signature.
            </p>
        </div>

        <div class="mt-7 pt-5 border-t border-forest/10 flex items-center justify-between">
            <a href="{{ route('admin.biography.edit') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                <span>Edit Biography & Profile Details</span>
                <span>→</span>
            </a>
            <a href="{{ route('about') }}#biography" target="_blank" class="text-xs text-slate-400 hover:text-forest">View live ↗</a>
        </div>
    </div>

    <!-- 2. Career Achievements & Trophies -->
    <div class="bg-white border border-forest/15 rounded-2xl p-7 shadow-xs flex flex-col justify-between hover:border-gold hover:shadow-md transition-all group">
        <div>
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 rounded-xl bg-forest/5 text-forest flex items-center justify-center text-xl font-serif">
                    02
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-forest/10 text-forest">
                    {{ $achievementsCount }} Total Trophies & Records
                </span>
            </div>
            <h2 class="mt-5 font-serif text-2xl text-forest group-hover:text-gold transition-colors">Key Achievements & Trophy Case</h2>
            <p class="mt-1 text-xs uppercase tracking-wider text-slate-400 font-medium">Milestones, Titles & Honors</p>

            <div class="mt-5 space-y-2 bg-ivory/50 p-4 rounded-xl border border-forest/10">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Recent Highlights:</p>
                @forelse($recentAchievements as $achievement)
                    <div class="flex items-center justify-between text-xs py-1 border-b border-forest/5 last:border-0">
                        <span class="font-medium text-forest truncate max-w-xs">{{ $achievement->title }}</span>
                        <span class="text-slate-500 font-mono">{{ $achievement->year }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">No achievements recorded yet.</p>
                @endforelse
            </div>

            <p class="mt-4 text-xs text-slate-500 leading-relaxed">
                Add, reorder, and update trophies, international tour wins, Olympic appearances, and honorary awards listed on the About page.
            </p>
        </div>

        <div class="mt-7 pt-5 border-t border-forest/10 flex items-center justify-between gap-2">
            <a href="{{ route('admin.achievements.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest hover:text-gold transition-colors">
                <span>Manage Achievements ({{ $achievementsCount }})</span>
                <span>→</span>
            </a>
            <a href="{{ route('admin.achievements.create') }}" class="text-xs font-medium px-3 py-1.5 rounded bg-forest text-white hover:bg-gold hover:text-forest transition-colors">+ Add Trophy</a>
        </div>
    </div>

</div>
@endsection

