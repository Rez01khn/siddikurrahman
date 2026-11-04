@extends('layouts.admin')

@section('content')
<p class="text-xs uppercase tracking-[.18em] text-forest/60 mb-3">Content workspace</p>
<h1 class="font-serif text-3xl sm:text-4xl text-forest mb-8">{{ $title }}</h1>
<section class="bg-white border border-forest/10 rounded-xl p-7 sm:p-12 max-w-3xl"><span class="inline-block rounded-full bg-gold/20 text-forest px-4 py-2 text-xs font-semibold">Coming in Phase 3</span><h2 class="font-serif text-3xl text-forest mt-7">A space reserved for {{ mb_strtolower($title) }}.</h2><p class="mt-5 text-slate-500 text-sm leading-7 max-w-xl">This module is a preview of the administration structure. Create, edit, delete, upload, and publishing actions are not available yet. No website content is changed from this page.</p><a href="{{ route('admin.dashboard') }}" class="inline-block mt-8 bg-forest text-white rounded-lg px-5 py-3 text-sm">← Return to dashboard</a></section>
@endsection
