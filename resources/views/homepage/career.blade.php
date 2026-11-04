<section id="career" class="bg-forest text-white section-space relative overflow-hidden">
    <span aria-hidden="true" class="absolute -right-4 -bottom-20 font-display text-[350px] text-white/[.025] leading-none">SR</span>
    <div class="shell relative reveal"><div class="flex flex-wrap items-end justify-between gap-6"><x-section-heading :eyebrow="$section->text('eyebrow')" :title="$section->text('title')" :light="true"/><p class="text-sm text-white/75 max-w-xs mb-10 leading-6">{{ $section->text('description') }}</p></div>
        <div class="grid md:grid-cols-3 border-t border-white/25">
            @if($featuredAchievements->isNotEmpty() || $careerStatistics->isNotEmpty())
                @foreach($featuredAchievements->map(fn ($item) => ['value' => $item->event_date?->format('Y') ?? $item->year, 'label' => $item->title, 'description' => $item->description])->concat($careerStatistics->map(fn ($item) => ['value' => $item->value, 'label' => $item->label, 'description' => $item->description]))->take(3) as $highlight)
                    <div class="py-9 md:pr-8 border-b border-white/25"><p class="font-display text-gold text-5xl lg:text-6xl">{{ $highlight['value'] }}</p><h3 class="mt-5 text-sm">{{ $highlight['label'] }}</h3><p class="text-sm text-white/75 mt-3 leading-6">{{ $highlight['description'] }}</p></div>
                @endforeach
            @else
            @foreach([1, 2, 3] as $highlight)
                @php
                    $value = $section->text("highlight_{$highlight}_value");
                    $label = $section->text("highlight_{$highlight}_heading");
                    $description = $section->text("highlight_{$highlight}_description");
                @endphp
                <div class="py-9 md:pr-8 border-b border-white/25"><p class="font-display text-gold {{ strlen($value) > 8 ? 'text-5xl lg:text-6xl' : 'text-6xl lg:text-7xl' }}">{{ $value }}</p><h3 class="mt-5 text-sm">{{ $label }}</h3><p class="text-sm text-white/75 mt-3 leading-6">{{ $description }}</p></div>
            @endforeach
            @endif
        </div>
        @if($featuredAchievements->isNotEmpty() || $careerStatistics->isNotEmpty())
        <p class="text-sm text-white/75 mt-6"><a href="{{ route('biography') }}#career" class="underline underline-offset-4">Explore the career and verification notes ↗</a></p>
        @else
        <p class="text-sm text-white/75 mt-6">{{ $section->text('source_prefix') }} <a class="underline underline-offset-4" href="https://siddikurrahman.com/" target="_blank" rel="noopener noreferrer">{{ $section->text('source_label') }}</a>. {{ $section->text('source_note') }}</p>
        @endif
    </div>
</section>
