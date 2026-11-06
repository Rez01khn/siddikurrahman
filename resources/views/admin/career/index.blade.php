@extends('layouts.admin')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="font-serif text-3xl sm:text-4xl text-forest font-semibold">{{ $type === 'achievements' ? 'Key Achievements' : $title }}</h1>
        <p class="text-sm text-slate-500 mt-1">Curated, source-backed career history and major championship honors.</p>
    </div>
    <a href="{{ route('admin.'.$type.'.create') }}" class="inline-flex items-center gap-2 bg-forest hover:bg-forest/90 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">
        <span>{{ $type === 'achievements' ? 'Add Achievement +' : 'Add record +' }}</span>
    </a>
</div>

<x-admin.homepage-feedback />

<form method="GET" class="bg-white rounded-2xl border border-forest/10 p-5 flex flex-wrap items-end gap-4 mb-6 shadow-xs">
    <div class="flex-1 min-w-44">
        <label for="q" class="block text-xs font-semibold text-slate-600 mb-2">Search</label>
        <input id="q" name="q" value="{{ request('q') }}" maxlength="100" placeholder="Search by title..." class="border border-forest/20 rounded-xl px-3.5 py-2.5 w-full text-sm outline-none focus:border-forest focus:ring-1 focus:ring-forest">
    </div>
    <div>
        <label for="status" class="block text-xs font-semibold text-slate-600 mb-2">Publication</label>
        <select id="status" name="status" class="border border-forest/20 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-forest bg-white">
            <option value="">All</option>
            <option value="published" @selected(request('status') === 'published')>Published</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
        </select>
    </div>
    <div class="flex items-center gap-2">
        <button type="submit" class="bg-forest hover:bg-forest/90 text-white rounded-xl px-6 py-2.5 text-sm font-semibold transition shadow-xs">
            Search
        </button>
        <a href="{{ route('admin.'.$type.'.index') }}" class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl px-5 py-2.5 text-sm font-semibold transition shadow-xs">
            Reset
        </a>
    </div>
</form>

<div class="bg-white rounded-2xl border border-forest/10 overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <caption class="sr-only">{{ $title }} records</caption>
            <thead class="bg-slate-50/80 text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-forest/10">
                <tr>
                    <th scope="col" class="py-4 px-6 w-20">Order</th>
                    <th scope="col" class="py-4 px-6">{{ $type === 'statistics' ? 'Metric' : 'Title / Year' }}</th>
                    <th scope="col" class="py-4 px-6 w-36">Status</th>
                    <th scope="col" class="py-4 px-6 w-44 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-forest/10 text-slate-700">
                @forelse($records as $record)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="py-4 px-6 font-mono text-slate-500">{{ $record->sort_order }}</td>
                    <td class="py-4 px-6 min-w-56">
                        <p class="font-bold text-slate-900 text-sm">{{ $type === 'statistics' ? $record->label : $record->title }}</p>
                        <p class="text-xs text-slate-500 mt-1">
                            {{ $type === 'statistics' ? $record->value : ($record->event_date?->format('j M Y') ?? $record->year) }}
                            @if($type === 'achievements' && $record->is_featured)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gold/15 text-gold border border-gold/30">★ Featured</span>
                            @endif
                        </p>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $record->is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $record->is_published ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                            {{ $record->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-3 text-xs">
                            <a class="font-medium text-forest hover:text-gold underline" href="{{ route('admin.'.$type.'.edit', $record->id) }}">Edit</a>
                            <form action="{{ route('admin.'.$type.'.toggle', $record->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button class="font-medium text-slate-500 hover:text-slate-800 underline">
                                    {{ $record->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-400">No matching records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">{{ $records->links() }}</div>
<p class="text-xs text-slate-500 mt-4">Change display order on the edit screen. Lower numbers appear first, then record ID for ties.</p>
@endsection
