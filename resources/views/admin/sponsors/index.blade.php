@extends('layouts.admin')
@section('content')
<div class="flex flex-wrap justify-between gap-4 mb-7">
    <div>
        <h1 class="font-serif text-3xl text-forest">Sponsors & Partners</h1>
        <p class="text-sm text-slate-500 mt-1">Manage brand partners displayed on the homepage marquee.</p>
    </div>
    <a class="bg-forest text-white rounded-lg px-5 py-3 text-sm self-start hover:bg-forest/90" href="{{ route('admin.sponsors.create') }}">Add sponsor +</a>
</div>

<x-admin.homepage-feedback />

<form method="GET" class="mb-6 flex flex-wrap gap-3 items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search sponsors..." class="border border-forest/20 rounded-lg px-4 py-2 text-sm bg-white min-w-[240px]">
    <select name="publication" class="border border-forest/20 rounded-lg px-4 py-2 text-sm bg-white">
        <option value="">All statuses</option>
        <option value="published" @selected(request('publication') === 'published')>Published</option>
        <option value="draft" @selected(request('publication') === 'draft')>Draft</option>
    </select>
    <button class="bg-forest text-white rounded-lg px-4 py-2 text-sm">Filter</button>
    @if(request()->hasAny(['q', 'publication']))
        <a href="{{ route('admin.sponsors.index') }}" class="text-xs text-forest underline ml-2">Clear</a>
    @endif
</form>

<div class="bg-white rounded-xl border border-forest/10 overflow-x-auto shadow-xs">
    <table class="w-full text-sm text-left">
        <caption class="sr-only">Sponsor management</caption>
        <thead class="bg-ivory/60 border-b border-forest/10">
            <tr>
                <th class="p-4 w-20">Order</th>
                <th class="p-4">Logo</th>
                <th class="p-4">Sponsor</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-forest/10">
            @forelse($sponsors as $sponsor)
                <tr class="hover:bg-slate-50/50">
                    <td class="p-4 font-mono text-xs text-slate-500">{{ $sponsor->sort_order }}</td>
                    <td class="p-4">
                        @if($sponsor->imageUrl('logo'))
                            <img src="{{ $sponsor->imageUrl('logo') }}" alt="{{ $sponsor->name }}" class="h-8 max-w-[120px] object-contain">
                        @else
                            <span class="text-xs text-slate-400 italic">No image</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <p class="font-semibold text-forest">{{ $sponsor->name }}</p>
                        @if($sponsor->website_url)
                            <a href="{{ $sponsor->website_url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-gold hover:underline">{{ $sponsor->website_url }} ↗</a>
                        @endif
                    </td>
                    <td class="p-4">
                        <span class="inline-flex px-2 py-0.5 rounded text-xs {{ $sponsor->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $sponsor->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="p-4 text-right space-x-2">
                        <a class="text-sm text-forest font-medium underline" href="{{ route('admin.sponsors.edit', $sponsor) }}">Edit</a>
                        <form action="{{ route('admin.sponsors.destroy', $sponsor) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this sponsor?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="p-8 text-slate-500 text-center" colspan="5">No sponsors found. Add a sponsor to display on the homepage.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $sponsors->links() }}</div>
@endsection
