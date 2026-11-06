@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <p class="text-xs uppercase tracking-[.2em] text-forest/60 mb-2 font-medium">Administration</p>
    <h1 class="text-3xl sm:text-4xl font-serif text-forest">Website Management Studio</h1>
    <p class="mt-2 text-sm text-slate-500 leading-6">Welcome, {{ auth()->user()->name }}. Manage all dynamic content of the homepage and about page from the sidebar.</p>
</div>

<!-- Quick Statistics -->
<div class="mb-8">
    <h2 class="text-sm font-semibold uppercase tracking-wider text-slate-500 mb-4">Active Content Overview</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($metrics as $metric)
            <a href="{{ route($metric['route']) }}" class="block bg-white border border-forest/10 rounded-xl p-5 hover:border-gold hover:shadow-sm transition-all group">
                <div class="flex justify-between items-center text-xs text-slate-500">
                    <span class="group-hover:text-forest transition-colors font-medium">{{ $metric['label'] }}</span>
                    <span class="text-gold group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform">↗</span>
                </div>
                <p class="mt-3 text-3xl text-forest font-serif">{{ number_format($metric['value']) }}</p>
            </a>
        @endforeach
    </div>
</div>

<!-- Page Architecture Breakdown -->
<div class="bg-white border border-forest/10 rounded-xl overflow-hidden shadow-xs">
    <div class="p-6 border-b border-forest/10 flex items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-xl text-forest">Page Content Mapping</h2>
            <p class="text-xs text-slate-500 mt-1">Direct breakdown of which module controls which public page section.</p>
        </div>
        <span class="text-xs text-forest bg-forest/5 px-3 py-1.5 rounded-full font-medium">Page-Centric Architecture</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs uppercase tracking-wider text-slate-500 bg-ivory/60 border-b border-forest/10">
                <tr>
                    <th scope="col" class="px-6 py-3.5">Target Page</th>
                    <th scope="col" class="px-6 py-3.5">Section Name</th>
                    <th scope="col" class="px-6 py-3.5">Editor Link</th>
                    <th scope="col" class="px-6 py-3.5">Public Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-forest/10">
                <tr>
                    <td class="px-6 py-4 font-semibold text-forest">Homepage</td>
                    <td class="px-6 py-4 text-slate-600">Hero Banner & Highlights</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.homepage.index') }}" class="font-medium text-forest underline hover:text-gold">Edit Hero & Highlights →</a></td>
                    <td class="px-6 py-4 text-emerald-700 font-medium">● Live</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-semibold text-forest">Homepage</td>
                    <td class="px-6 py-4 text-slate-600">Tournament Schedule</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.tournaments.index') }}" class="font-medium text-forest underline hover:text-gold">Manage Tournaments →</a></td>
                    <td class="px-6 py-4 text-emerald-700 font-medium">● Live</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-semibold text-forest">Homepage</td>
                    <td class="px-6 py-4 text-slate-600">Coaching & Pro Tips</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.coaching.index') }}" class="font-medium text-forest underline hover:text-gold">Manage Tips →</a></td>
                    <td class="px-6 py-4 text-emerald-700 font-medium">● Live</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-semibold text-forest">Homepage</td>
                    <td class="px-6 py-4 text-slate-600">Photos & Videos Gallery</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.photos.index') }}" class="font-medium text-forest underline hover:text-gold">Manage Gallery →</a></td>
                    <td class="px-6 py-4 text-emerald-700 font-medium">● Live</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-semibold text-forest">Homepage</td>
                    <td class="px-6 py-4 text-slate-600">Sponsors & Partners</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.sponsors.index') }}" class="font-medium text-forest underline hover:text-gold">Manage Sponsors →</a></td>
                    <td class="px-6 py-4 text-emerald-700 font-medium">● Live</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-semibold text-forest">About Page</td>
                    <td class="px-6 py-4 text-slate-600">Biography, Personal Facts & Career Story</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.biography.edit') }}" class="font-medium text-forest underline hover:text-gold">Edit Biography →</a></td>
                    <td class="px-6 py-4 text-emerald-700 font-medium">● Live</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-semibold text-forest">About Page</td>
                    <td class="px-6 py-4 text-slate-600">Key Achievements & Trophies</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.achievements.index') }}" class="font-medium text-forest underline hover:text-gold">Manage Achievements →</a></td>
                    <td class="px-6 py-4 text-emerald-700 font-medium">● Live</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-semibold text-slate-500">Global</td>
                    <td class="px-6 py-4 text-slate-600">Header, Footer, Logo & Social Links</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.settings.edit') }}" class="font-medium text-forest underline hover:text-gold">Website Settings →</a></td>
                    <td class="px-6 py-4 text-emerald-700 font-medium">● Live</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
