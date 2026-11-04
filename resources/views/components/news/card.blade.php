@props(['article'])
<article {{ $attributes }}>
@if($article->imageUrl())<a href="{{ route('news.show',$article->slug) }}" class="block image-wrap"><img src="{{ $article->imageUrl() }}" alt="{{ $article->image_caption ?: $article->title }}" class="h-72 md:h-80 w-full object-cover" loading="lazy"></a>@endif
<p class="eyebrow text-forest/70 mt-6">{{ $article->category->name }} · {{ $article->publication_date->format('j M Y') }}</p><h2 class="font-display uppercase text-4xl text-forest mt-3"><a href="{{ route('news.show',$article->slug) }}">{{ $article->title }}</a></h2>@if($article->excerpt)<p class="text-sm text-ink/75 leading-7 mt-4">{{ $article->excerpt }}</p>@endif<a href="{{ route('news.show',$article->slug) }}" class="editorial-link text-forest mt-6">Read story <span aria-hidden="true">↗</span></a>
</article>
