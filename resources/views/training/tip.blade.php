@extends('layouts.site')
@section('title', $tip->seo_title ?: $tip->title)
@section('meta_description', $tip->seo_description ?: $tip->excerpt)
@section('content')
<x-community.hero :title="$tip->title" eyebrow="Coaching & tips" /><article class="section-space shell"><div class="max-w-3xl mx-auto">@if($tip->imageUrl())<img class="w-full mb-10" src="{{ $tip->imageUrl() }}" alt="{{ $tip->image_alt ?: $tip->title }}" fetchpriority="high">@endif<p class="text-xl text-forest leading-8 mb-9">{{ $tip->excerpt }}</p><div class="text-ink/75 leading-8 space-y-6"><x-biography.paragraphs :text="$tip->description" /></div><div class="mt-10"><x-tournaments.external-link :url="$tip->source_url" label="Original source" /></div><a class="editorial-link text-forest mt-10" href="{{ route('coaching-tips.index') }}">All coaching tips ↗</a></div></article>
@endsection