@extends('layouts.admin')
@section('content')
<h1 class="font-serif text-3xl text-forest mb-7">Add an existing homepage section.</h1>
<x-admin.homepage-feedback />
<section class="bg-white border border-forest/10 rounded-xl p-6 max-w-2xl"><p class="text-sm text-slate-500 leading-7 mb-6">Only the ten existing layouts are supported. Adding a missing section saves its original fallback text so you can edit it. It does not create a new page or content module.</p>
@if($available->isNotEmpty())<form method="POST" action="{{ route('admin.homepage.store') }}" class="space-y-6">@csrf<label class="block text-sm">Section<select name="section_key" required class="block w-full mt-2 border border-forest/25 rounded-lg p-3">@foreach($available as $key => $definition)<option value="{{ $key }}" @selected(old('section_key') === $key)>{{ $definition['label'] }}</option>@endforeach</select></label><button class="bg-forest text-white rounded-lg px-5 py-3 text-sm">Add section</button></form>@else<p class="text-sm text-forest">All homepage sections are already configured.</p>@endif
<a href="{{ route('admin.homepage.index') }}" class="inline-block text-sm underline mt-6">Back to homepage management</a></section>
@endsection
