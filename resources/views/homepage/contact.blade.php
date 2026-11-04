<section id="contact" class="bg-forest text-ivory pt-24">
    <div class="shell">
        <div class="grid lg:grid-cols-[1.5fr_1fr] gap-12 pb-20 border-b border-white/20">
            <div><p class="eyebrow text-gold mb-6">{{ $section->text('eyebrow') }}</p><h2 class="font-display text-7xl md:text-9xl uppercase leading-[.9]"><x-homepage.lines :text="$section->text('title')" /> <span class="text-gold">{{ $section->text('subtitle') }}</span></h2></div>
            <div class="lg:pt-12"><p class="text-white/70 max-w-sm leading-7 mb-8">{{ $section->text('description') }}</p><a href="{{ route('contact') }}" class="block text-xl border-b border-gold/50 pb-4">{{ $section->text('button_text') === 'Official contact details coming soon' ? 'Send an enquiry' : $section->text('button_text') }} <span class="text-gold">↗</span></a>@if($section->text('verification_note') !== 'Design preview · contact channel awaiting confirmation.')<p class="text-sm text-white/75 mt-4">{{ $section->text('verification_note') }}</p>@endif</div>
        </div>
    </div>
</section>
