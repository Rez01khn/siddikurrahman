<section id="schedule" class="section-space bg-[#eae9df]">
    <div class="shell reveal"><div class="grid lg:grid-cols-[1fr_1.6fr] gap-12 lg:gap-24"><div><x-section-heading :eyebrow="$section->text('eyebrow')" :title="$section->text('title')"/><p class="text-sm text-ink/75 leading-7 max-w-xs">{{ $section->text('description') }}</p><p class="inline-block border border-forest/25 px-3 py-2 text-sm uppercase tracking-widest mt-8">{{ $tournamentPreview->isNotEmpty() ? 'Published schedule' : $section->text('status') }}</p><a href="{{ route('tournaments.index') }}" class="editorial-link text-forest mt-8">Full schedule & results <span aria-hidden="true">↗</span></a></div>
        <div class="border-t border-forest/30">
            @forelse($tournamentPreview as $tournament)
                <a href="{{ route('tournaments.show', $tournament) }}" class="flex items-center gap-6 border-b border-forest/25 py-8">
                    <span class="font-display text-3xl text-forest/40">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="flex-1"><p class="eyebrow text-forest/60 mb-2">{{ $tournament->start_date->format('j M') }} – {{ $tournament->end_date->format('j M Y') }} / {{ config('tournaments.statuses.'.$tournament->status) }}</p><h3 class="font-display text-3xl uppercase text-forest">{{ $tournament->name }}</h3><p class="text-sm text-ink/75 mt-2">{{ $tournament->location() }}</p>
                    @if($tournament->publishedResult)<p class="text-sm text-forest mt-3">{{ config('tournaments.result_statuses.'.$tournament->publishedResult->status) }}@if($tournament->publishedResult->final_position !== null) · Position {{ $tournament->publishedResult->final_position }}@endif</p>@endif</div><span class="text-forest text-xl" aria-hidden="true">↗</span>
                </a>
            @empty
                <div class="border-b border-forest/25 py-10"><h3 class="font-display text-3xl uppercase text-forest">The next chapter is being prepared.</h3><p class="text-sm text-ink/75 leading-7 mt-4">No confirmed tournaments are published here yet. Dates and results will appear after review.</p></div>
            @endforelse
        </div></div>
    </div>
</section>