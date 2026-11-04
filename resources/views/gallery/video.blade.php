@extends('layouts.site')
@section('title'){{ $video->title }} · Video
@endsection
@section('content')
<x-gallery.hero :title="$video->title" eyebrow="Video gallery" /><section class="section-space shell">@if($video->embedUrl())<iframe src="{{ $video->embedUrl() }}" title="{{ $video->title }}" class="w-full aspect-video border-0 bg-forest" loading="lazy" allow="encrypted-media; picture-in-picture; fullscreen" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe><p class="text-xs text-ink/60 mt-4">Playback is provided by the linked video platform and depends on its availability and embed permissions.</p>@else<p class="text-sm text-ink/65">This video link is unavailable.</p>@endif<div class="space-y-5 max-w-3xl mt-10 text-sm text-ink/75"><x-biography.paragraphs :text="$video->description" /></div>@if($video->video_date)<p class="text-xs text-ink/60 mt-7">{{ $video->video_date->format('j F Y') }}</p>@endif<div class="mt-8"><x-tournaments.external-link :url="$video->video_url" label="Watch on the original platform" /></div></section>
@endsection
