@extends('layouts.site')

@section('content')
@if($sections->first()?->section_key !== 'hero')<div class="h-24" aria-hidden="true"></div>@endif
@foreach($sections as $section)
    @if($section->section_key === 'contact')
        <aside class="bg-[#eae9df] px-5 py-4 text-center text-sm text-ink/75 leading-5">Illustrative stock photographs do not depict Siddikur Rahman. Approved content is published after review.</aside>
    @endif
    @include('homepage.'.$section->section_key, ['section' => $section])
@endforeach
@endsection
