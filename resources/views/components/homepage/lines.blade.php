@props(['text', 'accentLast' => false])
@foreach(explode("\n", $text) as $line)
    <span @class(['text-gold' => $accentLast && $loop->last])>{{ $line }}</span>@unless($loop->last)<br>@endunless
@endforeach
