@extends('layouts.admin-base')

@section('body')
<main id="admin-main" class="min-h-screen grid lg:grid-cols-2">
    <section class="hidden lg:flex bg-forest text-white p-16 xl:p-24 flex-col justify-between relative overflow-hidden">
        <a href="{{ route('home') }}" class="text-xl font-serif text-gold">SR. <span class="ml-3 font-sans text-sm text-white">Siddikur Rahman</span></a>
        <div class="relative z-10"><p class="text-xs uppercase tracking-[.25em] text-gold mb-8">Behind the experience</p><h1 class="font-serif text-6xl xl:text-7xl leading-tight">A purposeful<br>presence.<br><span class="text-gold">Beautifully managed.</span></h1><p class="mt-8 text-white/70 max-w-md leading-7">A considered space for the stories, moments, and people behind the game.</p></div>
        <p class="text-xs text-white/60">Official website · Private administration</p>
        <span aria-hidden="true" class="absolute -right-20 -bottom-20 text-[350px] font-serif text-white/[.03]">SR</span>
    </section>
    <section class="flex items-center justify-center px-6 py-16 sm:px-12">
        <div class="w-full max-w-md"><a href="{{ route('home') }}" class="text-sm text-forest hover:underline">← Back to website</a><p class="mt-14 text-xs uppercase tracking-[.2em] text-forest">Administrator access</p><h2 class="font-serif text-4xl sm:text-5xl text-forest mt-4">Welcome back.</h2><p class="text-sm text-slate-500 mt-4 mb-9 leading-6">Sign in to your website workspace. Access is limited to authorized administrators.</p>
            @if(session('status'))<p role="status" class="mb-6 p-4 bg-forest/5 border border-forest/20 rounded-lg text-sm text-forest">{{ session('status') }}</p>@endif
            @if($errors->any())<div role="alert" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800"><p class="font-semibold">Unable to sign in</p><ul class="mt-2 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-6">
                @csrf
                <div><label for="email" class="block text-sm font-medium mb-2">Username or Email</label><input id="email" name="email" type="text" value="{{ old('email') }}" required autocomplete="username" maxlength="254" autofocus placeholder="admin" @error('email') aria-invalid="true" @enderror class="w-full rounded-lg border border-forest/25 bg-white px-4 py-3.5 text-base"></div>
                <div><label for="password" class="block text-sm font-medium mb-2">Password</label><input id="password" name="password" type="password" required autocomplete="current-password" maxlength="1024" @error('password') aria-invalid="true" @enderror class="w-full rounded-lg border border-forest/25 bg-white px-4 py-3.5 text-base"></div>
                <button type="submit" class="w-full bg-forest text-white rounded-lg py-4 px-5 text-sm font-semibold flex justify-between hover:bg-forest/90">Sign in to dashboard <span aria-hidden="true">↗</span></button>
            </form>
            <p class="mt-8 text-xs text-slate-500 leading-6">No public registration. For access assistance, contact the website owner.</p>
        </div>
    </section>
</main>
@endsection
