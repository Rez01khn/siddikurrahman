@php
    $plain = static fn ($text) => trim(html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $pageTitle = $plain($__env->yieldContent('title', $siteSettings?->seo_title ?: 'Siddikur Rahman — A game of purpose'));
    $pageDescription = $plain($__env->yieldContent('meta_description', $siteSettings?->seo_description ?: 'Discover the journey of Siddikur Rahman, professional golfer from Bangladesh.'));
    $canonical = rtrim(config('app.url'), '/').(request()->path() === '/' ? '' : '/'.request()->path());
    $indexable = config('seo.indexable') && !request()->routeIs('admin.*') && !request()->query();
    $record = $article ?? $tip ?? $project ?? $tournament ?? $album ?? $video ?? $biography ?? null;
    $socialImage = $record?->imageUrl($record instanceof \App\Models\Biography ? 'profile_image' : ($record instanceof \App\Models\PhotoAlbum ? 'cover_image' : 'image')) ?? $siteSettings?->imageUrl('logo');
@endphp
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta name="robots" content="{{ $indexable ? 'index, follow' : 'noindex, nofollow' }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:type" content="{{ isset($article) || isset($tip) ? 'article' : 'website' }}">
<meta property="og:site_name" content="{{ $siteSettings?->site_name ?: 'Siddikur Rahman' }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $canonical }}">
@if($socialImage)<meta property="og:image" content="{{ $socialImage }}">@endif
<meta name="twitter:card" content="{{ $socialImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">