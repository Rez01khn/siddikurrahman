@extends('layouts.admin-base', ['title' => 'Please wait'])
@section('body')
<main id="admin-main" class="min-h-screen grid place-items-center p-6"><div class="max-w-md bg-white border border-forest/15 rounded-xl p-9"><p class="text-xs uppercase tracking-widest text-forest">Too many requests</p><h1 class="font-serif text-4xl text-forest mt-4">Take a short pause.</h1><p class="mt-5 text-sm leading-7 text-slate-600">Too many sign-in attempts have been made. Please wait a minute before trying again.</p><a class="inline-block mt-7 text-sm underline text-forest" href="{{ route('admin.login') }}">Return to sign in</a></div></main>
@endsection
