@props(['note'])
@if($note)<p {{ $attributes->class(['text-xs leading-6 break-words']) }}>Verification: {{ $note }}</p>@endif
