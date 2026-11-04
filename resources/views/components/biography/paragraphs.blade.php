@props(['text'])
@foreach(preg_split('/\R\s*\R/u', (string) $text) as $paragraph)
    @if(trim($paragraph) !== '')<p {{ $attributes->class(['whitespace-pre-line leading-8']) }}>{{ $paragraph }}</p>@endif
@endforeach
