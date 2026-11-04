<section id="journal" class="section-space shell reveal">
    <div class="flex flex-wrap items-end justify-between gap-4"><x-section-heading :eyebrow="$section->text('eyebrow')" :title="$section->text('title')"/><div class="mb-10"><p class="text-sm text-ink/75 mb-5">{{ $section->text('description') }}</p><a class="editorial-link text-forest" href="{{ route('news.index') }}">All stories <span aria-hidden="true">↗</span></a></div></div>
    <div class="grid md:grid-cols-2 gap-9 lg:gap-16">
        @forelse($latestArticles as $article)<x-news.card :article="$article" :class="$loop->last && !$loop->first ? 'md:pt-20' : ''" />@empty<div class="md:col-span-2 border-y border-forest/20 py-10"><p class="text-sm text-ink/75 leading-7">No reviewed articles have been published yet. Stories will appear here when available.</p></div>@endforelse
    </div>
    <div class="mt-12 border-y border-forest/20 py-6 flex flex-wrap items-center justify-between gap-5"><div><h3 class="font-display text-3xl uppercase text-forest">{{ $section->text('results_heading') }}</h3><p class="text-sm text-ink/75 mt-2">For player scorecards and tournament results, explore the schedule.</p></div><a href="{{ $section->buttonUrl($visibleAnchors) }}" class="editorial-link text-forest">{{ $section->text('button_text') }} <span aria-hidden="true">↗</span></a></div>
</section>
