@extends('layouts.admin')
@section('content')
<div class="mb-7">
    <a href="{{ route('admin.sponsors.index') }}" class="text-sm text-forest underline">← Back to Sponsors</a>
    <h1 class="font-serif text-3xl text-forest mt-3">{{ $sponsor->exists ? 'Edit' : 'Add' }} Sponsor</h1>
</div>

<x-admin.homepage-feedback />

<form method="POST" enctype="multipart/form-data" action="{{ $sponsor->exists ? route('admin.sponsors.update', $sponsor) : route('admin.sponsors.store') }}" class="space-y-6">
    @csrf 
    @if($sponsor->exists) @method('PUT') @endif

    <section class="bg-white border border-forest/10 rounded-xl p-6 space-y-6 shadow-xs">
        <h2 class="font-serif text-xl text-forest">Sponsor Information</h2>
        <x-admin.career-field name="name" label="Sponsor / Partner Name" :value="$sponsor->name" :maxlength="255" required />
        <x-admin.career-field name="website_url" label="Website URL (https://...)" :value="$sponsor->website_url" :maxlength="2048" />
        <x-admin.career-field name="category" label="Category / Tier (Optional)" :value="$sponsor->category" :maxlength="100" />
        <x-admin.career-field name="description" label="Description / Partnership Note (Optional)" type="textarea" :value="$sponsor->description" :maxlength="5000" />
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Sponsor Logo</label>
            @if($sponsor->imageUrl('logo'))
                <div class="mb-3 p-4 bg-slate-50 border border-slate-200 rounded-lg max-w-sm flex items-center justify-between">
                    <img src="{{ $sponsor->imageUrl('logo') }}" alt="{{ $sponsor->name }}" class="h-10 object-contain">
                    <label class="text-xs text-red-600 flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="remove_logo" value="1"> Remove logo
                    </label>
                </div>
            @endif
            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm border border-forest/20 rounded-lg p-2.5 bg-white">
            <p class="text-xs text-slate-500 mt-1">JPEG, PNG or WebP up to 5 MB.</p>
        </div>
    </section>

    <section class="bg-white border border-forest/10 rounded-xl p-6 space-y-4 shadow-xs">
        <h2 class="font-serif text-xl text-forest">Display Settings</h2>
        <div class="grid sm:grid-cols-2 gap-6 items-center">
            <div>
                <label class="flex items-center gap-3 text-sm font-medium text-slate-700 cursor-pointer">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $sponsor->is_published ?? true))>
                    <span>Display this sponsor on the website</span>
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Display Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $sponsor->sort_order ?? 0) }}" class="w-32 border border-forest/20 rounded-lg p-2 text-sm">
                <span class="block text-xs text-slate-500 mt-1">Lower numbers appear first.</span>
            </div>
        </div>
    </section>

    <div class="flex items-center gap-4">
        <button type="submit" class="bg-forest text-white rounded-lg px-7 py-3 text-sm font-semibold hover:bg-forest/90">Save Sponsor</button>
        <a href="{{ route('admin.sponsors.index') }}" class="border border-forest/25 rounded-lg px-5 py-3 text-sm text-slate-700 hover:bg-slate-50">Cancel</a>
    </div>
</form>
@endsection
