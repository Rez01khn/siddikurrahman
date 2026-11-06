@extends('layouts.admin')
@section('content')
<h1 class="font-serif text-3xl text-forest mb-7">{{ $type === 'achievements' ? ($record->exists ? 'Edit Achievement' : 'Add Achievement') : $title }}</h1>
<x-admin.homepage-feedback />

<form action="{{ $record->exists ? route('admin.'.$type.'.update', $record->id) : route('admin.'.$type.'.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($record->exists) @method('PUT') @endif

    @if($type === 'achievements')
        <section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6">
            <x-admin.career-field name="title" label="Title" :value="$record->title" :maxlength="255" required />
            <x-admin.career-field name="year" label="Year" type="number" :value="$record->year" required />
            <x-admin.career-field name="description" label="Description" type="textarea" :value="$record->description" :maxlength="10000" required />
        </section>

        <section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6">
            <x-admin.career-image :record="$record" :show-remove="false" />
        </section>

        <input type="hidden" name="is_published" value="{{ $record->exists ? ($record->is_published ? '1' : '0') : '1' }}">
        <input type="hidden" name="is_featured" value="{{ $record->exists ? ($record->is_featured ? '1' : '0') : '1' }}">
    @elseif($type === 'statistics')
        <section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6">
            <x-admin.career-field name="label" label="Statistic label" :value="$record->label" :maxlength="255" required />
            <x-admin.career-field name="value" label="Verified value (entered manually)" :value="$record->value" :maxlength="100" required />
            <x-admin.career-field name="description" label="Description" type="textarea" :value="$record->description" :maxlength="10000" />
            <x-admin.career-field name="source_note" label="Source / verification note" type="textarea" :value="$record->source_note" :maxlength="2000" required />
            <x-admin.career-field name="sort_order" label="Display order (0–10000)" type="number" :value="$record->sort_order" required />
            <input type="hidden" name="is_published" value="0">
            <label class="flex gap-3 items-center text-sm"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $record->is_published))>Published</label>
        </section>
    @else
        <section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6">
            <x-admin.career-field name="title" label="Title" :value="$record->title" :maxlength="255" required />
            <div class="grid sm:grid-cols-2 gap-6">
                <x-admin.career-field name="year" label="Year" type="number" :value="$record->year" />
                <x-admin.career-field name="event_date" label="Exact date" type="date" :value="$record->event_date?->format('Y-m-d')" />
            </div>
            <x-admin.career-field name="description" label="Description" type="textarea" :value="$record->description" :maxlength="10000" required />
            <x-admin.career-field name="source_note" label="Source / verification note" type="textarea" :value="$record->source_note" :maxlength="2000" />
        </section>
        <section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6">
            <x-admin.career-image :record="$record" />
            <x-admin.career-field name="image_alt" label="Image description / caption" :value="$record->image_alt" :maxlength="255" />
        </section>
        <section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6">
            <x-admin.career-field name="sort_order" label="Display order (0–10000)" type="number" :value="$record->sort_order" required />
            <input type="hidden" name="is_published" value="0">
            <label class="flex gap-3 items-center text-sm"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $record->is_published))>Published</label>
        </section>
    @endif

    <div class="flex gap-4">
        <button type="submit" class="bg-forest text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-forest/90">Save record</button>
        <a href="{{ route('admin.'.$type.'.index') }}" class="border border-forest/25 px-6 py-3 rounded-lg text-sm hover:bg-forest/5">Cancel</a>
    </div>
</form>

@if($record->exists)
    <details class="mt-10 p-5 border border-red-200 rounded-xl bg-red-50/20">
        <summary class="text-sm text-red-800 cursor-pointer font-medium">Delete this record</summary>
        <p class="text-sm text-slate-500 my-4">Deletion permanently removes this record and its managed image. Unpublish it instead to retain it privately.</p>
        <form method="POST" action="{{ route('admin.'.$type.'.destroy', $record->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Are you sure you want to permanently delete this achievement?')" class="text-sm text-red-800 border border-red-300 rounded-lg px-5 py-3 hover:bg-red-100">Confirm permanent deletion</button>
        </form>
    </details>
@endif
@endsection
