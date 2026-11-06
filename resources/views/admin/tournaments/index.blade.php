@extends('layouts.admin')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="font-serif text-3xl sm:text-4xl text-forest font-semibold">Tournament Schedule</h1>
        <p class="text-sm text-slate-500 mt-1">Manage tournament entries displayed in the "My Schedule" calendar on the homepage.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.tournaments.create') }}" class="inline-flex items-center gap-2 bg-forest hover:bg-forest/90 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-xs">
            <span>Add tournament +</span>
        </a>
    </div>
</div>

<x-admin.homepage-feedback />
<x-tournaments.filters admin />

<div class="bg-white border border-forest/10 rounded-2xl overflow-x-auto shadow-xs">
    <table class="text-sm w-full text-left">
        <caption class="sr-only">Tournament schedule management</caption>
        <thead class="bg-ivory/60 border-b border-forest/10 text-xs uppercase tracking-wider text-slate-500 font-semibold">
            <tr>
                <th class="p-4 w-20">Order</th>
                <th class="p-4">Tournament</th>
                <th class="p-4">Dates & Location</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-forest/10">
            @forelse($tournaments as $tournament)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="p-4 font-mono text-xs text-slate-500">{{ $tournament->sort_order }}</td>
                    <td class="p-4">
                        <a class="text-forest font-semibold hover:underline" href="{{ route('admin.tournaments.edit', $tournament) }}">{{ $tournament->name }}</a>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $tournament->tour }}</p>
                    </td>
                    <td class="p-4 text-xs text-slate-600">
                        <p class="font-medium text-slate-700">{{ $tournament->formattedDateRange() }}</p>
                        <p class="text-slate-500 mt-0.5">{{ $tournament->venue }}{{ $tournament->city ? ', ' . $tournament->city : '' }}</p>
                    </td>
                    <td class="p-4">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $tournament->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $tournament->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="p-4 text-right">
                        <div class="inline-flex items-center gap-2 justify-end">
                            <a href="{{ route('admin.tournaments.edit', $tournament) }}" class="inline-flex items-center justify-center bg-forest hover:bg-forest/90 text-white rounded-lg px-3.5 py-1.5 text-xs font-semibold transition shadow-xs">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.tournaments.toggle', $tournament) }}" class="inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="field" value="is_published">
                                <button type="submit" class="inline-flex items-center justify-center {{ $tournament->is_published ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }} rounded-lg px-3.5 py-1.5 text-xs font-semibold transition shadow-xs">
                                    {{ $tournament->is_published ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this tournament?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg px-3.5 py-1.5 text-xs font-semibold transition shadow-xs">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">No matching tournaments found. Add an event to display on the calendar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $tournaments->links() }}</div>
@endsection
