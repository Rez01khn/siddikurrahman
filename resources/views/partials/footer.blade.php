<footer class="bg-forest text-ivory">
    <div class="shell">
        <div class="py-12 grid md:grid-cols-3 gap-8">
            <a href="{{ route('home') }}" class="font-display text-4xl uppercase break-words">{{ $siteSettings?->site_name ?: 'Siddikur Rahman' }}<span class="text-gold">.</span></a>
            <div class="flex flex-wrap gap-x-7 gap-y-3 text-sm"><a href="{{ route('biography') }}">Biography</a>
<a href="{{ route('tournaments.index') }}">Tournaments</a>
<a href="{{ route('news.index') }}">News &amp; articles</a>
<a href="{{ route('photos.index') }}">Photos</a>
<a href="{{ route('videos.index') }}">Videos</a><a href="{{ route('in-the-bag') }}">In the bag</a><a href="{{ route('coaching-tips.index') }}">Coaching tips</a>
<a href="{{ route('sponsors.index') }}">Sponsors</a>
<a href="{{ route('social-works.index') }}">Social works</a></div>
            <div class="text-sm text-white/75 md:text-right leading-6"><p>{{ $siteSettings?->footer_text ?: 'From Bangladesh. For the love of the game.' }}</p>@if($siteSettings?->contact_address)<p>{{ $siteSettings->contact_address }}</p>@endif<a class="block mt-3 underline" href="{{ route('contact') }}">Contact</a><div class="flex flex-wrap md:justify-end gap-4 mt-4">@foreach(($siteSettings?->social_links ?? []) as $label=>$url)<x-tournaments.external-link :url="$url" :label="ucfirst($label)" />@endforeach</div></div>
        </div>
        <div class="border-t border-white/15 py-6 flex flex-wrap justify-between gap-4 text-sm text-white/75"><p>© {{ date('Y') }} Siddikur Rahman · Homepage design concept</p><a href="{{ in_array('top', $visibleAnchors, true) ? '#top' : '#main' }}">Back to top ↑</a></div>
    </div>
</footer>
