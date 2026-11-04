@extends('layouts.admin')
@section('content')
<x-admin.career-nav /><h1 class="font-serif text-3xl text-forest mb-7">{{ $title }}</h1><x-admin.homepage-feedback />
<form action="{{ $record->exists ? route('admin.'.$type.'.update', $record->id) : route('admin.'.$type.'.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">@csrf @if($record->exists) @method('PUT') @endif
<section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6">
@if($type === 'statistics')
<x-admin.career-field name="label" label="Statistic label" :value="$record->label" :maxlength="255" required /><x-admin.career-field name="value" label="Verified value (entered manually)" :value="$record->value" :maxlength="100" required />
@else
<x-admin.career-field name="title" label="Title" :value="$record->title" :maxlength="255" required />
<div class="grid sm:grid-cols-2 gap-6"><x-admin.career-field name="year" label="Year (or supply an exact date)" type="number" :value="$record->year" /><x-admin.career-field name="event_date" label="Exact date (optional if year is supplied)" type="date" :value="$record->event_date?->format('Y-m-d')" /></div>
@if($type === 'achievements')
<label class="block text-sm">Category<select name="category" required class="block border border-forest/25 rounded-lg p-3 w-full mt-2"><option value="">Choose category</option>@foreach(config('career.categories') as $key => $label)<option value="{{ $key }}" @selected(old('category', $record->category) === $key)>{{ $label }}</option>@endforeach</select></label><x-admin.career-field name="event_name" label="Tournament / event name (optional)" :value="$record->event_name" :maxlength="255" />
@endif
@endif
<x-admin.career-field name="description" label="Description" type="textarea" :value="$record->description" :maxlength="10000" :required="$type !== 'statistics'" />
<x-admin.career-field name="source_note" label="Source / verification note" type="textarea" :value="$record->source_note" :maxlength="2000" :required="$type === 'statistics'" />
<p class="text-xs text-slate-500">Source notes are required for every published record. Only publish reviewed facts. Statistics are never calculated from these records.</p>
</section>
@if($type !== 'statistics')<section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6"><x-admin.career-image :record="$record" /><x-admin.career-field name="image_alt" label="Image description / caption" :value="$record->image_alt" :maxlength="255" /></section>@endif
<section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6"><x-admin.career-field name="sort_order" label="Display order (0–10000)" type="number" :value="$record->sort_order" required />
<input type="hidden" name="is_published" value="0"><label class="flex gap-3 items-center text-sm"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $record->is_published))>Published (reviewed and approved)</label>
@if($type === 'achievements')<input type="hidden" name="is_featured" value="0"><label class="flex gap-3 items-center text-sm"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $record->is_featured))>Featured on homepage career highlights</label>@endif
</section><div class="flex gap-4"><button class="bg-forest text-white px-6 py-3 rounded-lg text-sm">Save record</button><a href="{{ route('admin.'.$type.'.index') }}" class="border border-forest/25 px-6 py-3 rounded-lg text-sm">Cancel</a></div></form>
@if($record->exists)<details class="mt-10 p-5 border border-red-200 rounded-xl"><summary class="text-sm text-red-800 cursor-pointer">Delete this record</summary><p class="text-sm text-slate-500 my-4">Deletion permanently removes this record and its managed image. Unpublish it instead to retain it privately.</p><form method="POST" action="{{ route('admin.'.$type.'.destroy', $record->id) }}">@csrf @method('DELETE')<button class="text-sm text-red-800 border border-red-300 rounded-lg px-5 py-3">Confirm permanent deletion</button></form></details>@endif
@endsection
