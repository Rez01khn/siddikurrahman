@extends('layouts.admin')
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.tournaments.index') }}" class="text-sm underline text-forest">← Back to Tournament Schedule</a>
    <h1 class="font-serif text-3xl text-forest mt-3">{{ $title }}</h1>
</div>

<x-admin.homepage-feedback />

<form action="{{ $tournament->exists ? route('admin.tournaments.update', $tournament) : route('admin.tournaments.store') }}" method="POST" class="space-y-6">
    @csrf
    @if($tournament->exists) @method('PUT') @endif

    <input type="hidden" name="is_published" value="{{ $tournament->exists ? ($tournament->is_published ? '1' : '0') : '1' }}">
    <input type="hidden" name="is_featured" value="{{ $tournament->exists ? ($tournament->is_featured ? '1' : '0') : '0' }}">
    <input type="hidden" name="sort_order" value="{{ $tournament->exists ? $tournament->sort_order : 0 }}">

    <section class="bg-white border border-forest/10 rounded-2xl p-5 sm:p-7 space-y-6 shadow-xs">
        <x-admin.career-field name="name" label="Tournament name" :value="$tournament->name" :maxlength="255" required />
        <x-admin.career-field name="tour" label="Series / tour" :value="$tournament->tour" :maxlength="255" />

        <div class="grid md:grid-cols-3 gap-6">
            <x-admin.career-field name="venue" label="Venue" :value="$tournament->venue" :maxlength="255" />
            <x-admin.career-field name="city" label="City" :value="$tournament->city" :maxlength="255" />
            <x-admin.career-field name="country" label="Country" :value="$tournament->country" :maxlength="255" />
        </div>

        <div class="grid sm:grid-cols-2 gap-6">
            <x-admin.career-field name="start_date" label="Start date" type="date" :value="$tournament->start_date?->format('Y-m-d')" required />
            <x-admin.career-field name="end_date" label="End date" type="date" :value="$tournament->end_date?->format('Y-m-d')" required />
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tournament status</label>
            <select name="status" required class="block w-full border border-forest/20 rounded-xl p-3 text-sm outline-none focus:border-forest bg-white">
                @foreach(config('tournaments.statuses') as $key => $label)
                    <option value="{{ $key }}" @selected(old('status', $tournament->status) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="text-xs text-slate-500 mt-1.5">Status indicates whether the tournament is upcoming, ongoing, or completed.</p>
        </div>
    </section>

    <div class="flex gap-4">
        <button type="submit" class="bg-forest hover:bg-forest/90 text-white rounded-xl px-6 py-3 text-sm font-semibold transition shadow-xs">
            Save tournament
        </button>
        <a href="{{ route('admin.tournaments.index') }}" class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl px-6 py-3 text-sm font-semibold transition shadow-xs">
            Cancel
        </a>
    </div>
</form>
@endsection
