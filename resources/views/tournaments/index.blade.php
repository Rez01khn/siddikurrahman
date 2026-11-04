@extends('layouts.site')
@section('title', 'Tournament Schedule & Results · Siddikur Rahman')
@section('meta_description', 'Published tournament schedules and sourced player results for Siddikur Rahman.')
@section('content')
<section id="top" class="bg-forest text-white pt-48 pb-24"><div class="shell"><p class="eyebrow text-gold mb-7">On tour</p><h1 class="font-display uppercase text-[clamp(3.5rem,9vw,9rem)] leading-[.9]">The season.<br><span class="text-gold">Shot by shot.</span></h1><p class="mt-9 text-white/75 leading-7 max-w-xl">Follow the published schedule, explore the venues, and find reviewed results from the course.</p><nav aria-label="Schedule sections" class="flex flex-wrap gap-7 mt-9 text-sm"><a href="#upcoming">Upcoming ↓</a><a href="#ongoing">Ongoing ↓</a><a href="#past">Past & results ↓</a></nav></div></section>
<div class="shell pt-14"><x-admin.homepage-feedback /><x-tournaments.filters /></div>
@foreach(['upcoming' => ['Upcoming tournaments', 'The next tee time.', $upcoming], 'ongoing' => ['Ongoing tournaments', 'Inside the ropes.', $ongoing], 'past' => ['Past tournaments & results', 'Rounds on record.', $past]] as $id => [$eyebrow, $heading, $items])
<section id="{{ $id }}" class="section-space {{ $id === 'ongoing' ? 'bg-[#eae9df]' : '' }}"><div class="shell"><x-section-heading :eyebrow="$eyebrow" :title="$heading" />
@forelse($items as $tournament)<x-tournaments.card :tournament="$tournament" />@empty<p class="border-t border-forest/20 pt-7 text-sm text-ink/65 leading-7">No published {{ $id === 'past' ? 'past' : $id }} tournaments match these filters. Confirmed events will appear when published.</p>@endforelse
<div class="mt-8">{{ $items->links() }}</div></div></section>
@endforeach
@endsection
