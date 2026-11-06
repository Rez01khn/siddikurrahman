@extends('layouts.admin')
@section('content')
<div class="flex flex-wrap justify-between gap-5 mb-8">
    <div>
        <h1 class="font-serif text-3xl text-forest">{{ $title }}</h1>
        <p class="text-sm text-slate-500 mt-1">Manage tips displayed in the Coaching and Tips section of the homepage.</p>
    </div>
    <a class="bg-forest text-white px-5 py-3 rounded-lg text-sm self-start hover:bg-forest/90" href="{{ route('admin.'.$type.'.create') }}">Add coaching tip +</a>
</div>

<x-admin.homepage-feedback />

<form method="GET" class="mb-6 flex flex-wrap gap-3 items-center">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search tips..." class="border border-forest/20 rounded-lg px-4 py-2 text-sm bg-white min-w-[240px]">
    <select name="publication" class="border border-forest/20 rounded-lg px-4 py-2 text-sm bg-white">
        <option value="">All statuses</option>
        <option value="published" @selected(request('publication') === 'published')>Published</option>
        <option value="draft" @selected(request('publication') === 'draft')>Draft</option>
    </select>
    <button class="bg-forest text-white rounded-lg px-4 py-2 text-sm">Filter</button>
    @if(request()->hasAny(['q', 'publication']))
        <a href="{{ route('admin.'.$type.'.index') }}" class="text-xs text-forest underline ml-2">Clear</a>
    @endif
</form>

<div class="bg-white overflow-x-auto border border-forest/10 rounded-xl shadow-xs">
    <table class="w-full text-left text-sm">
        <thead class="bg-ivory/60 border-b border-forest/10">
            <tr>
                <th class="p-4 w-20">Order</th>
                <th class="p-4">Image</th>
                <th class="p-4">Title & Category</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-forest/10">
            @forelse($records as $record)
                <tr class="hover:bg-slate-50/50">
                    <td class="p-4 font-mono text-xs text-slate-500">{{ $record->sort_order }}</td>
                    <td class="p-4">
                        @if($record->imageUrl('image'))
                            <img src="{{ $record->imageUrl('image') }}" alt="{{ $record->title }}" class="h-10 w-16 object-cover rounded border border-forest/10">
                        @else
                            <span class="text-xs text-slate-400 italic">No image</span>
                        @endif
                    </td>
                    <td class="p-4">
                        <span class="font-semibold text-forest block">{{ $record->title }}</span>
                        <span class="text-xs text-slate-500 mt-1 block">{{ $record->category }}</span>
                    </td>
                    <td class="p-4">
                        <span class="inline-flex px-2 py-0.5 rounded text-xs {{ $record->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $record->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="p-4 text-right space-x-2">
                        <a class="text-sm text-forest font-medium underline" href="{{ route('admin.'.$type.'.edit', $record->id) }}">Edit</a>
                        <form action="{{ route('admin.'.$type.'.destroy', $record->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this tip?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">No coaching tips found. Add coaching content for the homepage.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-7">{{ $records->links() }}</div>
@endsection
