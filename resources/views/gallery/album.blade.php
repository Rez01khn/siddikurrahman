@extends('layouts.site')
@section('title'){{ $album->title }} · Photo Album
@endsection
@section('content')
<x-gallery.hero :title="$album->title" eyebrow="Photo album" /><section class="section-space shell"><div class="max-w-3xl mb-12 space-y-5 text-sm text-ink/75"><x-biography.paragraphs :text="$album->description" /></div><div class="grid md:grid-cols-2 gap-10">@forelse($photos as $photo)<x-gallery.photo :photo="$photo" />@empty<p class="text-sm text-ink/65">No published photos are available in this album.</p>@endforelse</div><div class="mt-10">{{ $photos->links() }}</div></section>
@endsection
