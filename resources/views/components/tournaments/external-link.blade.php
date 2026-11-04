@props(['url', 'label' => 'Official source'])
@if($url && \App\Rules\SafeWebUrl::allows($url))<a {{ $attributes->class(['underline underline-offset-4 text-sm']) }} href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $label }} ↗</a>@endif
