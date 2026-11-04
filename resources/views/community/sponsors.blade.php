@extends('layouts.site')
@section('title','Sponsors · Siddikur Rahman')
@section('content')
<x-community.hero title="Shared ambition." eyebrow="Sponsors & partners" /><section class="section-space shell"><x-admin.homepage-feedback /><x-community.filters :categories="$categories" />@forelse($sponsors->getCollection()->groupBy('is_featured') as $featured=>$group)<h2 class="font-display text-4xl uppercase text-forest mb-9">{{ $featured?'Featured sponsors':'Partnerships' }}</h2><div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10 mb-14">@foreach($group as $sponsor)<x-community.sponsor :sponsor="$sponsor" />@endforeach</div>@empty<p class="text-sm text-ink/65 leading-7">No verified sponsors have been published for this selection. Approved names and logos will appear when available.</p>@endforelse<div class="mt-10">{{ $sponsors->links() }}</div></section>
@endsection
