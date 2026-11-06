@extends('layouts.admin')

@section('content')
<!-- Breadcrumb -->
<nav class="flex items-center gap-2 text-xs text-slate-500 mb-4" aria-label="Breadcrumb">
    <svg class="w-4 h-4 text-gold/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    <a href="{{ route('admin.pages.home') }}" class="hover:text-forest transition-colors font-medium">Homepage</a>
    <span class="text-slate-400">›</span>
    <span class="text-slate-700 font-semibold">Hero & Banners</span>
</nav>

<!-- Page Header -->
<div class="flex flex-wrap items-center justify-between gap-4 mb-7">
    <div>
        <div class="flex items-center gap-3">
            <div class="text-forest">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="font-serif text-3xl sm:text-4xl text-forest font-semibold tracking-tight">Hero & Banners</h1>
        </div>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Manage the hero section and promotional banners on your homepage.</p>
    </div>
    <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gold/40 bg-gold/10 text-forest text-xs sm:text-sm font-medium hover:bg-gold/20 transition-all shadow-xs">
        <svg class="w-4 h-4 text-forest/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        <span>View Homepage</span>
        <span class="text-gold">↗</span>
    </a>
</div>

@if(session('status'))
    <div id="status-alert" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between gap-3">
        <div class="flex items-center gap-2.5">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('status') }}</span>
        </div>
        <button type="button" onclick="document.getElementById('status-alert').remove()" class="text-emerald-600 hover:text-emerald-900 text-sm">✕</button>
    </div>
@endif

<!-- Dynamic Toast Container for instant actions -->
<div id="toast" class="fixed top-5 right-5 z-50 transform transition-all duration-300 translate-y-[-100px] opacity-0 pointer-events-none px-4 py-3 rounded-xl shadow-lg border text-sm font-medium flex items-center gap-2.5">
    <span id="toast-icon">✓</span>
    <span id="toast-message">Action successful</span>
</div>

<!-- Main Top Grid: Hero Edit Form (Left) & Real Live Preview (Right) -->
<div class="grid lg:grid-cols-2 gap-6 items-start">

    <!-- Card 1: Hero Section Form -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-xs">
        <form action="{{ route('admin.homepage.update', $hero) }}" method="POST" enctype="multipart/form-data" id="hero-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="sort_order" value="{{ $hero->sort_order ?? 10 }}">
            <input type="hidden" name="subtitle" value="{{ $hero->subtitle ?? 'Professional Golfer' }}">
            <input type="hidden" name="is_active" id="hero-form-is-active" value="{{ $hero->is_active ? '1' : '0' }}">

            <!-- Card Header: Title & Interactive Published Switch -->
            <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-serif text-xl text-forest font-bold tracking-tight">Hero Section</h2>
                    </div>
                </div>

                <!-- Live AJAX Toggle Switch -->
                <div class="flex items-center gap-3">
                    <span id="publish-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $hero->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }} transition-colors">
                        <span id="publish-badge-dot" class="w-1.5 h-1.5 rounded-full {{ $hero->is_active ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                        <span id="publish-badge-text">{{ $hero->is_active ? 'Published' : 'Unpublished' }}</span>
                    </span>

                    <button type="button" id="publish-toggle-btn" data-url="{{ route('admin.homepage.toggle', $hero) }}" data-csrf="{{ csrf_token() }}" class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $hero->is_active ? 'bg-emerald-600' : 'bg-slate-300' }}" role="switch" aria-checked="{{ $hero->is_active ? 'true' : 'false' }}">
                        <span class="sr-only">Toggle Hero Visibility</span>
                        <span id="publish-toggle-circle" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out {{ $hero->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
                </div>
            </div>

            <!-- Inputs and Image 2-Column Split -->
            <div class="grid sm:grid-cols-2 gap-5">
                <!-- Left Sub-column: Text Fields -->
                <div class="space-y-4">
                    <!-- Golfer's Name -->
                    <div>
                        <label for="hero-name-input" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Golfer's Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-lg">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <input type="text" id="hero-name-input" name="title" value="{{ old('title', $hero->title ?? 'Mohammed Siddikur Rahman') }}" required class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm border border-slate-200 rounded-lg focus:border-forest focus:ring-1 focus:ring-forest outline-none transition text-slate-800 font-medium">
                        </div>
                    </div>

                    <!-- Professional Title -->
                    <div>
                        <label for="hero-title-input" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Professional Title <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative rounded-lg">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <input type="text" id="hero-title-input" name="additional_data[eyebrow]" value="{{ old('additional_data.eyebrow', $hero->text('eyebrow') ?? 'Professional Golfer') }}" required class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm border border-slate-200 rounded-lg focus:border-forest focus:ring-1 focus:ring-forest outline-none transition text-slate-800 font-medium">
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div>
                        <label for="hero-desc-input" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Short Description
                        </label>
                        <textarea id="hero-desc-input" name="description" rows="4" maxlength="200" class="w-full p-2.5 text-xs sm:text-sm border border-slate-200 rounded-lg focus:border-forest focus:ring-1 focus:ring-forest outline-none transition text-slate-800 leading-relaxed resize-none">{{ old('description', $hero->description ?? 'Representing Bangladesh on the global golf stage.') }}</textarea>
                        <div class="flex justify-end text-[10px] text-slate-400 mt-1">
                            <span id="hero-char-count">45</span> / 200
                        </div>
                    </div>
                </div>

                <!-- Right Sub-column: Hero Image Box -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Hero Image
                    </label>

                    @php
                        $defaultHeroImg = asset('images/portfolio/siddikur_29092023_9851_ati-Picsart-AiImageEnhancer.jpg');
                        $currentHeroImg = $hero->imageUrl('primary') ?: $defaultHeroImg;
                    @endphp

                    <div class="relative w-full aspect-4/3 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 group/img">
                        <img id="hero-img-preview" src="{{ $currentHeroImg }}" alt="Hero Golfer Preview" class="w-full h-full object-cover object-top">
                        
                        <!-- Crop button overlay -->
                        <button type="button" class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-black/75 hover:bg-black text-white text-xs font-medium backdrop-blur-xs transition shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Crop</span>
                        </button>
                    </div>

                    <!-- Image Action Buttons -->
                    <div class="flex items-center gap-2 mt-3">
                        <label for="hero-image-file" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold cursor-pointer transition shadow-xs">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Change Image</span>
                        </label>
                        <input id="hero-image-file" name="image" type="file" accept="image/*" class="hidden">

                        <button type="button" id="hero-remove-img-btn" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-rose-200 bg-rose-50/60 hover:bg-rose-100 text-rose-600 text-xs font-semibold transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Remove</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Save Action Button -->
            <div class="mt-8 pt-5 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-7 py-2.5 rounded-xl bg-forest hover:bg-forest/90 text-white text-xs sm:text-sm font-semibold transition shadow-sm">
                    <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Card 2: Faithful Live Preview of the Real Hero Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-xs flex flex-col justify-between">
        <div>
            <!-- Live Preview Header -->
            <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <h2 class="font-serif text-xl font-bold text-slate-900 tracking-tight">Live Preview</h2>
                </div>

                <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700">
                    <span id="live-indicator-dot" class="w-2 h-2 rounded-full {{ $hero->is_active ? 'bg-emerald-600 animate-pulse' : 'bg-slate-400' }}"></span>
                    <span id="live-indicator-text">{{ $hero->is_active ? 'Live on Website' : 'Hidden from Website' }}</span>
                </div>
            </div>

            <!-- Faithful Canvas: Matches the public Hero Section perfectly -->
            <div class="bg-[#0D2216] text-white rounded-2xl overflow-hidden relative shadow-inner flex flex-col justify-between min-h-[460px] pb-6">
                <!-- Golfer Image on Right with Mask (same as public site) -->
                <div class="absolute inset-y-0 right-0 w-[58%] h-full pointer-events-none overflow-hidden select-none">
                    <img id="live-hero-img" src="{{ $currentHeroImg }}" alt="Siddikur Rahman" class="w-full h-full object-cover object-top" style="mask-image: linear-gradient(to right, transparent 0%, black 35%, black 100%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 35%, black 100%);">
                </div>

                <!-- Contour SVG lines in bottom right (same as public site) -->
                <svg class="absolute right-0 bottom-16 w-56 h-36 text-white/10 pointer-events-none" viewBox="0 0 240 150" fill="none">
                    <path d="M35 150C75 82 150 58 240 72" stroke="currentColor" stroke-width="1"/>
                    <path d="M75 150C115 104 175 88 240 96" stroke="currentColor" stroke-width="0.75"/>
                </svg>

                <!-- Top Navbar of Preview -->
                <div class="relative z-10 flex items-center justify-between text-[10px] px-6 py-4 border-b border-white/10">
                    <div class="flex items-center gap-2">
                        <span class="font-serif font-bold text-gold text-base tracking-tighter">SR.</span>
                        <div class="leading-none">
                            <span class="block font-semibold text-white tracking-wider text-[9px]">Siddikur Rahman</span>
                            <span class="block text-[7px] text-gold/80 uppercase tracking-widest">GOLF WEBSITE</span>
                        </div>
                    </div>

                    <div class="hidden sm:flex items-center gap-3 text-white/70 text-[9px]">
                        <span class="text-white font-medium">Home</span>
                        <span>Career</span>
                        <span>Gallery</span>
                        <span>Coaching</span>
                        <span>About</span>
                        <span>Contact</span>
                    </div>

                    <div class="flex items-center gap-1.5 text-white/70">
                        <span class="w-4 h-4 rounded-full bg-white/10 flex items-center justify-center text-[8px]">f</span>
                        <span class="w-4 h-4 rounded-full bg-white/10 flex items-center justify-center text-[8px]">𝕏</span>
                        <span class="w-4 h-4 rounded-full bg-white/10 flex items-center justify-center text-[8px]">in</span>
                    </div>
                </div>

                <!-- Hero Content (NO BUTTONS - pure authentic layout) -->
                <div class="relative z-10 px-6 py-8 max-w-[62%]">
                    <div class="flex items-center gap-2 mb-2.5">
                        <p id="live-eyebrow" class="text-[9px] sm:text-[10px] uppercase tracking-[0.22em] text-white/85 font-medium">
                            {{ $hero->text('eyebrow') ?? 'PROFESSIONAL GOLFER' }}
                        </p>
                        <span class="w-8 h-px bg-white/40"></span>
                    </div>

                    <h3 id="live-title" class="font-serif text-2xl sm:text-3xl lg:text-[34px] leading-[1.08] text-white font-normal mb-3 whitespace-pre-line">
                        {{ $hero->title ?? "Mohammad\nSiddikur Rahman" }}
                    </h3>

                    <p id="live-desc" class="font-serif italic text-xs sm:text-sm text-white/90 leading-snug max-w-sm whitespace-pre-line">
                        {{ $hero->description ?? "Representing Bangladesh on\nthe global golf stage." }}
                    </p>
                </div>

                <!-- Overlapping Floating Card: CAREER HIGHLIGHTS (Exact replica of public site) -->
                <div class="relative z-10 px-4 sm:px-6 mt-auto">
                    <div class="bg-[#FAF7F0] border border-[#E8E2D5] rounded-xl p-3 shadow-md text-forest grid grid-cols-4 gap-1 text-center divide-x divide-[#E2DCD0]">
                        <div class="px-1 py-1">
                            <div class="text-sm mb-0.5">🏆</div>
                            <p class="text-[9px] font-semibold text-[#1E1E1E] leading-tight">Professional Golfer</p>
                            <span class="text-[8px] text-[#1E1E1E]/60">(Since 2005)</span>
                        </div>
                        <div class="px-1 py-1">
                            <div class="text-sm mb-0.5">🌐</div>
                            <p class="text-[9px] font-semibold text-[#1E1E1E] leading-tight">International Tour</p>
                            <span class="text-[8px] text-[#1E1E1E]/60">Competitor</span>
                        </div>
                        <div class="px-1 py-1">
                            <div class="text-sm mb-0.5">🤝</div>
                            <p class="text-[9px] font-semibold text-[#1E1E1E] leading-tight">Olympics Rio 2016</p>
                            <span class="text-[8px] text-[#1E1E1E]/60">Bangladesh</span>
                        </div>
                        <div class="px-1 py-1">
                            <div class="text-sm mb-0.5">⛳</div>
                            <p class="text-[9px] font-semibold text-[#1E1E1E] leading-tight">Asian Tour Champ</p>
                            <span class="text-[8px] text-[#1E1E1E]/60">(2010 & 2013)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Bottom Section: Homepage Promotional Banners (Cleaned from Achievements & Coaching) -->
<div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-7 shadow-xs mt-6">
    <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-slate-100 mb-6">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="text-forest">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="font-serif text-xl sm:text-2xl text-forest font-bold tracking-tight">Homepage Banners</h2>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Manage promotional banners and special feature sections on your homepage.</p>
        </div>

        <a href="{{ route('admin.homepage.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-forest hover:bg-forest/90 text-white text-xs sm:text-sm font-semibold transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Add Banner</span>
        </a>
    </div>

    <!-- Banners Table (Achievements and Coaching removed as they are separate dedicated modules) -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-slate-50/80 text-slate-600 text-[11px] uppercase tracking-wider font-semibold border-y border-slate-100">
                <tr>
                    <th scope="col" class="py-3.5 px-4 w-12 text-center">#</th>
                    <th scope="col" class="py-3.5 px-4 w-44">Banner Image</th>
                    <th scope="col" class="py-3.5 px-4">Title & Section</th>
                    <th scope="col" class="py-3.5 px-4 w-44">Link Target</th>
                    <th scope="col" class="py-3.5 px-4 w-32">Status</th>
                    <th scope="col" class="py-3.5 px-4 w-32">Display Order</th>
                    <th scope="col" class="py-3.5 px-4 w-28 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($banners as $index => $banner)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-4 text-center font-medium text-slate-500">{{ $index + 1 }}</td>
                        <td class="py-4 px-4">
                            <div class="w-24 h-14 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 shadow-2xs">
                                @if($banner->imageUrl('primary'))
                                    <img src="{{ $banner->imageUrl('primary') }}" alt="{{ $banner->text('title') }}" class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('images/portfolio/beyond-fishing.jpg') }}" alt="Banner" class="w-full h-full object-cover">
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <p class="font-bold text-slate-900 text-sm">{{ $banner->definition()['label'] ?? 'Promotional Banner' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ \Illuminate\Support\Str::limit($banner->text('title'), 60) }}</p>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 font-mono">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                <span>{{ $banner->button_url ?: '#'.$banner->section_key }}</span>
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $banner->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $banner->is_active ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>
                                {{ $banner->is_active ? 'Published' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <span class="text-slate-400">⠿</span>
                                <span class="w-14 px-2 py-1 border border-slate-200 rounded-lg text-center font-mono text-xs bg-slate-50 text-slate-700 block">{{ $banner->sort_order }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.homepage.edit', $banner) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    <span>Edit</span>
                                </a>
                                <form method="POST" action="{{ route('admin.homepage.toggle', $banner) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600 transition shadow-2xs" title="{{ $banner->is_active ? 'Hide banner' : 'Show banner' }}">
                                        {{ $banner->is_active ? '👁' : '✕' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 px-4 text-center text-slate-400">
                            No additional promotional banners configured. Click "+ Add Banner" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Scripts for Real-time Live Preview & Instant AJAX Publish/Unpublish Toggle -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('hero-name-input');
    const titleInput = document.getElementById('hero-title-input');
    const descInput = document.getElementById('hero-desc-input');
    const charCount = document.getElementById('hero-char-count');
    const imageInput = document.getElementById('hero-image-file');
    const removeBtn = document.getElementById('hero-remove-img-btn');

    const liveTitle = document.getElementById('live-title');
    const liveEyebrow = document.getElementById('live-eyebrow');
    const liveDesc = document.getElementById('live-desc');
    const heroImgPreview = document.getElementById('hero-img-preview');
    const liveHeroImg = document.getElementById('live-hero-img');

    // Real-time text preview
    if (nameInput && liveTitle) {
        nameInput.addEventListener('input', () => {
            liveTitle.textContent = nameInput.value || 'Mohammed Siddikur Rahman';
        });
    }

    if (titleInput && liveEyebrow) {
        titleInput.addEventListener('input', () => {
            liveEyebrow.textContent = (titleInput.value || 'PROFESSIONAL GOLFER').toUpperCase();
        });
    }

    if (descInput && liveDesc && charCount) {
        const updateDesc = () => {
            liveDesc.textContent = descInput.value || 'Representing Bangladesh on the global golf stage.';
            charCount.textContent = descInput.value.length;
        };
        descInput.addEventListener('input', updateDesc);
        updateDesc();
    }

    // Image change preview
    if (imageInput) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (heroImgPreview) heroImgPreview.src = e.target.result;
                    if (liveHeroImg) liveHeroImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            const defaultImg = "{{ asset('images/portfolio/siddikur_29092023_9851_ati-Picsart-AiImageEnhancer.jpg') }}";
            if (heroImgPreview) heroImgPreview.src = defaultImg;
            if (liveHeroImg) liveHeroImg.src = defaultImg;
            if (imageInput) imageInput.value = '';
        });
    }

    // Instant AJAX Publish / Unpublish Toggle
    const toggleBtn = document.getElementById('publish-toggle-btn');
    const toggleCircle = document.getElementById('publish-toggle-circle');
    const badge = document.getElementById('publish-badge');
    const badgeDot = document.getElementById('publish-badge-dot');
    const badgeText = document.getElementById('publish-badge-text');
    const formIsActive = document.getElementById('hero-form-is-active');
    const liveDot = document.getElementById('live-indicator-dot');
    const liveText = document.getElementById('live-indicator-text');
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');

    function showToast(msg, isSuccess = true) {
        toastMessage.textContent = msg;
        toast.className = `fixed top-5 right-5 z-50 transform transition-all duration-300 translate-y-0 opacity-100 px-4 py-3 rounded-xl shadow-lg border text-sm font-medium flex items-center gap-2.5 ${isSuccess ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200'}`;
        setTimeout(() => {
            toast.className = 'fixed top-5 right-5 z-50 transform transition-all duration-300 translate-y-[-100px] opacity-0 pointer-events-none px-4 py-3 rounded-xl shadow-lg border text-sm font-medium flex items-center gap-2.5';
        }, 3000);
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', async function () {
            const url = toggleBtn.dataset.url;
            const csrf = toggleBtn.dataset.csrf;
            toggleBtn.disabled = true;

            try {
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!res.ok) throw new Error('Toggle request failed');
                const data = await res.json();
                const active = Boolean(data.is_active);

                // Update switch UI
                if (active) {
                    toggleBtn.classList.remove('bg-slate-300');
                    toggleBtn.classList.add('bg-emerald-600');
                    toggleCircle.classList.remove('translate-x-0');
                    toggleCircle.classList.add('translate-x-5');
                    toggleBtn.setAttribute('aria-checked', 'true');

                    badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 transition-colors';
                    badgeDot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-600';
                    badgeText.textContent = 'Published';

                    if (liveDot) liveDot.className = 'w-2 h-2 rounded-full bg-emerald-600 animate-pulse';
                    if (liveText) liveText.textContent = 'Live on Website';
                    if (formIsActive) formIsActive.value = '1';

                    showToast('Hero section is now published live on the website!');
                } else {
                    toggleBtn.classList.remove('bg-emerald-600');
                    toggleBtn.classList.add('bg-slate-300');
                    toggleCircle.classList.remove('translate-x-5');
                    toggleCircle.classList.add('translate-x-0');
                    toggleBtn.setAttribute('aria-checked', 'false');

                    badge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200 transition-colors';
                    badgeDot.className = 'w-1.5 h-1.5 rounded-full bg-slate-400';
                    badgeText.textContent = 'Unpublished';

                    if (liveDot) liveDot.className = 'w-2 h-2 rounded-full bg-slate-400';
                    if (liveText) liveText.textContent = 'Hidden from Website';
                    if (formIsActive) formIsActive.value = '0';

                    showToast('Hero section is now hidden / unpublished.');
                }
            } catch (err) {
                showToast('Failed to update status. Please try again.', false);
            } finally {
                toggleBtn.disabled = false;
            }
        });
    }
});
</script>
@endsection
