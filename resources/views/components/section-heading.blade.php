@props(['eyebrow', 'title', 'light' => false])
<div {{ $attributes->class(['mb-10', 'text-white' => $light]) }}>
    <p class="eyebrow mb-4 {{ $light ? 'text-gold' : 'text-forest' }}">{{ $eyebrow }}</p>
    <h2 class="font-display text-5xl md:text-7xl uppercase leading-[.96] text-balance">{{ $title }}</h2>
</div>
