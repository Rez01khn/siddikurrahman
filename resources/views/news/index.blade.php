@extends('layouts.site')
@section('title','News & Articles · Siddikur Rahman')
@section('meta_description','Published news, interviews and articles from Siddikur Rahman’s official website.')
@section('content')
<section id="top" class="bg-forest text-white pt-48 pb-24"><div class="shell"><p class="eyebrow text-gold mb-7">The journal</p><h1 class="font-display uppercase text-[clamp(4rem,9vw,9rem)] leading-[.9]">Stories from<br><span class="text-gold">inside the ropes.</span></h1><p class="max-w-xl text-white/75 leading-7 mt-9">Published news, perspectives and press coverage from the world of golf.</p></div></section>
<section class="section-space shell"><x-admin.homepage-feedback /><x-news.filters :categories="$categories" />
@php($selectedCategory = $categories->firstWhere('id',request('category')))
@if($selectedCategory?->description)<p class="text-sm text-ink/70 leading-7 mb-10">{{ $selectedCategory->description }}</p>@endif
<div class="grid md:grid-cols-2 gap-x-12 gap-y-16">@forelse($articles as $article)<x-news.card :article="$article" />@empty<p class="text-sm text-ink/65 leading-7">No published articles match these filters. Reviewed stories will appear here when available.</p>@endforelse</div><div class="mt-12">{{ $articles->links() }}</div></section>
@endsection
