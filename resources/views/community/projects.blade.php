@extends('layouts.site')
@section('title','Social Works · Siddikur Rahman')
@section('content')
<x-community.hero title="Beyond the fairway." eyebrow="Social works" /><section class="section-space shell"><x-admin.homepage-feedback /><x-community.filters /><div class="grid md:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-16">@forelse($projects as $project)<x-community.project-card :project="$project" />@empty<p class="text-sm text-ink/65 leading-7">No verified social projects have been published for this selection. Reviewed activities will appear here when available.</p>@endforelse</div><div class="mt-10">{{ $projects->links() }}</div></section>
@endsection
