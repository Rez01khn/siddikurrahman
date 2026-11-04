@extends('layouts.admin')
@section('content')
<div class="flex flex-wrap justify-between gap-4 mb-7"><div><a class="text-sm underline text-forest" href="{{ route('admin.homepage.index') }}">← All homepage sections</a><h1 class="font-serif text-3xl text-forest mt-4">{{ $section->definition()['label'] }}</h1></div><a href="{{ route('home') }}" target="_blank" rel="noopener" class="self-end text-sm underline text-forest">View homepage ↗</a></div>
<x-admin.homepage-feedback />
<p class="text-sm text-slate-500 leading-7 mb-6">Plain text only. HTML is displayed as text. Line breaks are supported in the hero and contact headings. Blank optional fields use the original fallback text. Changes publish immediately when saved.</p>
<form action="{{ route('admin.homepage.update', $section) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
@csrf @method('PUT')
<section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6"><h2 class="text-xl font-serif text-forest">Main content</h2>
<x-admin.homepage-field name="title" label="Title" :value="$section->text('title')" :maxlength="500" required />
@foreach(['subtitle' => 'Subtitle / accent text', 'description' => 'Description', 'button_text' => 'Button / contact label', 'button_url' => 'Button link (local path or #anchor)'] as $field => $label)
    @continue($section->section_key === 'contact' && $field === 'button_url')
    @if($section->definition()['defaults'][$field] !== null || ($section->section_key === 'contact' && $field === 'button_url'))
        <x-admin.homepage-field :name="$field" :label="$section->section_key === 'hero' && $field === 'description' ? 'Hero subtitle' : $label" :value="$section->text($field)" :rows="$field === 'description' ? 4 : 2" :maxlength="$field === 'description' ? 5000 : ($field === 'button_text' ? 160 : ($field === 'button_url' ? 255 : 500))" />
    @endif
@endforeach
<p class="text-xs text-slate-500">Links accept paths such as /contact or anchors such as #journey. Links to hidden sections safely return to the main content.</p>
</section>
<section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-6"><h2 class="text-xl font-serif text-forest">Section details</h2><p class="text-sm text-slate-500">These fields match the existing layout, including preview text and verification labels. Do not publish unverified results or endorsements.</p>
@if(in_array($section->section_key, ['partners','community'], true))<p class="text-sm text-slate-500">Published featured records supply sponsor identities, project images and links. Manage these in Sponsors and Social Works. Historical sample fields remain stored but are no longer displayed.</p>@endif
@foreach($section->definition()['defaults']['additional_data'] as $key => $default)
    @continue(in_array($section->section_key, ['partners','community'], true) && $key !== 'eyebrow')
    @continue($section->section_key === 'journal' && !in_array($key, ['eyebrow','results_heading'],true))
    @continue($section->section_key === 'schedule' && (str_starts_with($key, 'item_') || in_array($key, ['date_label', 'results_label', 'verification_note'], true)))
    <x-admin.homepage-field :name="'additional_data.'.$key" :label="\Illuminate\Support\Str::headline($key)" :value="$section->text($key)" />
@endforeach
@if($section->section_key === 'journal')<p class="text-sm text-slate-500">Stories, dates and photographs now come from published News & Articles records. Homepage images and sample story fields are retained in storage but no longer used by this section.</p>@endif
@if($section->section_key === 'schedule')<p class="text-sm text-slate-500">Tournament preview rows now come from published Tournament Schedule records. Manage their names, dates, venues and results in that module; this screen controls the section heading, introduction and empty-state status label.</p>@endif
</section>
@if($section->definition()['image_enabled'] && !in_array($section->section_key, ['journal','community'], true))
<section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 space-y-7"><h2 class="text-xl font-serif text-forest">Photography</h2><p class="text-sm text-slate-500">JPEG, PNG, or WebP only. Maximum 5 MB, dimensions from 100 × 100 to 6000 × 6000 pixels. Update the image description and stock-photo labels when supplying approved athlete photography.</p>
@foreach(['primary' => 'image', 'secondary' => 'secondary_image'] as $slot => $field)
    @if($slot === 'primary' || $section->definition()['secondary_image_enabled'])
    <div class="space-y-3"><label for="{{ $field }}" class="block text-sm font-medium">{{ $slot === 'primary' ? 'Primary / welcome image' : 'Secondary image' }}</label>
    @if($section->imageUrl($slot))<img src="{{ $section->imageUrl($slot) }}" alt="Current {{ $slot }} image preview" class="w-full max-w-lg h-56 object-cover rounded-lg border border-forest/10" data-current-image="{{ $field }}">@else<p class="text-sm text-slate-500">No optional welcome image selected. The original layout is retained.</p>@endif
    <input id="{{ $field }}" name="{{ $field }}" type="file" accept="image/jpeg,image/png,image/webp" data-image-upload class="block w-full text-sm border border-forest/25 rounded-lg p-3">
    <p id="{{ $field }}-preview-status" role="status" class="text-xs text-slate-500"></p>
    <img id="{{ $field }}-preview" alt="Selected image preview" class="max-w-lg w-full h-56 object-contain border border-forest/15 rounded-lg" hidden>
    @if($section->hasImage($slot))<label class="flex items-center gap-3 text-sm"><input type="checkbox" name="{{ $slot === 'primary' ? 'remove_image' : 'remove_secondary_image' }}" value="1" @checked(old($slot === 'primary' ? 'remove_image' : 'remove_secondary_image'))>Remove uploaded image and use the original fallback</label>@endif
    </div>
    @endif
@endforeach
<p class="text-xs text-slate-500">A new upload takes priority if removal is also selected. Existing images remain unchanged after validation errors.</p>
</section>
@endif
<section class="bg-white rounded-xl border border-forest/10 p-5 sm:p-7 grid sm:grid-cols-2 gap-6"><div><input type="hidden" name="is_active" value="0"><label class="flex gap-3 items-center text-sm font-medium"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $section->is_active))>Show this section publicly</label><p class="text-xs text-slate-500 mt-3">Inactive sections do not appear on the homepage.</p></div><label class="block text-sm font-medium">Display order<input type="number" name="sort_order" value="{{ old('sort_order', $section->sort_order) }}" min="0" max="10000" required class="block w-full mt-2 border border-forest/25 rounded-lg p-3"><span class="block text-xs font-normal text-slate-500 mt-2">Lower numbers appear first.</span></label></section>
<div class="flex flex-wrap gap-4"><button class="bg-forest text-white rounded-lg px-7 py-3 text-sm font-semibold">Save homepage section</button><a href="{{ route('admin.homepage.index') }}" class="border border-forest/25 rounded-lg px-5 py-3 text-sm">Cancel</a></div>
</form>
<details class="mt-12 border border-red-200 rounded-xl p-5"><summary class="text-sm font-medium text-red-800 cursor-pointer">Delete customization / restore defaults</summary><p class="text-sm text-slate-600 leading-7 mt-4">This permanently deletes this saved section record and its uploaded images. The public section returns to its original fallback content and order. To hide it instead, disable its visibility above.</p><form action="{{ route('admin.homepage.destroy', $section) }}" method="POST" class="mt-5">@csrf @method('DELETE')<button class="border border-red-300 rounded-lg px-5 py-3 text-sm text-red-800">Confirm deletion and restore default content</button></form></details>
@endsection
