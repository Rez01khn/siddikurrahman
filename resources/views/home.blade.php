@extends('layouts.site')

@php
    $theme = $theme ?? config('theme');
    $info = $biography?->profileInfo() ?? $theme['personal_info'];
    $achievementsList = ($achievements && $achievements->isNotEmpty()) ? $achievements : collect($theme['achievements']);
    $scheduleList = ($tournaments && $tournaments->isNotEmpty()) ? $tournaments : collect($theme['schedule'] ?? []);
    $socials = array_merge($theme['socials'] ?? [], $siteSettings?->social_links ?? []);
@endphp

@section('content')
    {{-- =====================================================================
    1. HERO SECTION (Mohammad Siddikur Rahman — Representing Bangladesh)
    ===================================================================== --}}
    <section id="home-hero"
        class="relative min-h-[480px] lg:min-h-[530px] bg-[#0D2216] text-white flex flex-col justify-between">

        <div class="absolute inset-0 pointer-events-none overflow-hidden select-none">
            {{-- Right: Hero Golfer Image with Soft Gradient Mask --}}
            <div class="absolute inset-y-0 right-0 w-full lg:w-[65%] xl:w-[56%] 2xl:w-[50%] h-full">
                <img src="{{ $hero?->imageUrl('primary') ?: asset('images/portfolio/siddikur_29092023_9851_ati-Picsart-AiImageEnhancer.jpg') }}"
                    alt="Mohammad Siddikur Rahman mid-swing"
                    class="w-full h-full object-cover object-center select-none pointer-events-none"
                    style="mask-image: linear-gradient(to right, transparent 0%, black 25%, black 100%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 25%, black 100%);"
                    fetchpriority="high">

                <div
                    class="hidden lg:block absolute inset-y-0 left-0 w-32 xl:w-44 bg-gradient-to-r from-[#0D2216] to-transparent pointer-events-none">
                </div>

                <div
                    class="lg:hidden absolute inset-0 bg-gradient-to-r from-[#0D2216] via-[#0D2216]/85 to-[#0D2216]/50 pointer-events-none">
                </div>
            </div>

            {{-- Decorative Curved Contour Lines in Bottom-Right Corner --}}
            <svg class="absolute right-0 bottom-0 w-64 h-40 text-white/15 pointer-events-none" viewBox="0 0 240 150"
                fill="none" aria-hidden="true">
                <path d="M35 150C75 82 150 58 240 72" stroke="currentColor" stroke-width="1" />
                <path d="M75 150C115 104 175 88 240 96" stroke="currentColor" stroke-width="0.75" />
            </svg>
        </div>

        {{-- Hero Left Content --}}
        <div
            class="relative z-10 max-w-[1320px] mx-auto w-full px-5 sm:px-8 lg:px-12 pt-24 sm:pt-28 lg:pt-32 pb-4 sm:pb-6 lg:pb-8 flex-1 flex flex-col justify-center">
            <div class="max-w-[560px]">
                <div class="flex items-center gap-3.5 mb-3.5">
                    <span class="text-[11px] sm:text-xs uppercase tracking-[0.22em] text-white/85 font-medium">
                        {{ $hero?->text('eyebrow') ?: 'PROFESSIONAL GOLFER' }}
                    </span>
                    <span class="w-10 h-px bg-white/45"></span>
                </div>

                <h1
                    class="font-serif text-4xl sm:text-5xl lg:text-[60px] leading-[1.06] font-normal text-white tracking-[-0.01em]">
                    {!! nl2br(e($hero?->text('title') ?: "Mohammad\nSiddikur Rahman")) !!}
                </h1>

                <p
                    class="font-serif italic text-lg sm:text-xl lg:text-[22px] text-white/90 leading-[1.38] mt-3.5 max-w-[360px]">
                    {!! nl2br(e($hero?->text('description') ?: "Representing Bangladesh on\nthe global golf stage.")) !!}
                </p>
            </div>
        </div>

        {{-- Overlapping Floating Card: CAREER HIGHLIGHTS (Half in Hero, Half in Section Below) --}}
        <div id="career-highlights"
            class="relative z-20 max-w-[1320px] mx-auto w-full px-4 sm:px-6 lg:px-8 translate-y-1/2">
            <div
                class="bg-[#FAF7F0] border border-[#E8E2D5] rounded-[24px] shadow-[0_10px_25px_rgba(0,0,0,0.06)] p-6 sm:p-8 lg:p-10">
                <div class="flex flex-col lg:flex-row lg:items-center">
                    {{-- Left Heading Block --}}
                    <div class="lg:w-[35%] lg:pr-10 lg:border-r border-[#E2DCD0] pb-6 lg:pb-0">
                        <div class="flex items-center gap-3 mb-2.5">
                            <span class="text-[11px] uppercase tracking-[0.2em] text-[#1E1E1E] font-bold">
                                {{ $careerHighlights?->text('eyebrow') ?: 'CAREER HIGHLIGHTS' }}
                            </span>
                            <span class="w-8 h-[1.5px] bg-[#C5A47E]"></span>
                        </div>
                        <h2
                            class="font-serif text-3xl sm:text-[34px] lg:text-[38px] leading-[1.12] text-[#1E1E1E] font-normal tracking-[-0.01em]">
                            {!! nl2br(e($careerHighlights?->text('title') ?: "A Professional\nJourney Since 2005")) !!}
                        </h2>
                        <p class="text-xs sm:text-[13px] leading-[1.65] text-[#1E1E1E]/75 mt-3 max-w-[320px]">
                            {{ $careerHighlights?->text('description') ?: 'From local tournaments to international stages, Siddikur Rahman has consistently represented Bangladesh with pride.' }}
                        </p>
                    </div>

                    {{-- Right 4-Column Stat Highlights --}}
                    <div
                        class="lg:w-[65%] grid grid-cols-2 sm:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-[#E2DCD0] pt-6 lg:pt-0">

                        <div class="px-3 sm:px-4 py-4 lg:py-2 flex flex-col items-center text-center">
                            <div class="h-10 flex items-center justify-center text-[#0D2216] mb-3">
                                <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-[12.5px] font-semibold text-[#1E1E1E] leading-[1.4]">
                                {!! nl2br(e($careerHighlights?->text('stat_1_title') ?: "Professional\nGolfer")) !!}
                                <br>
                                <span class="text-[11.5px] font-normal text-[#1E1E1E]/70">{{ $careerHighlights?->text('stat_1_sub') ?: '(Since 2005)' }}</span>
                            </p>
                        </div>

                        <div class="px-3 sm:px-4 py-4 lg:py-2 flex flex-col items-center text-center">
                            <div class="h-10 flex items-center justify-center text-[#0D2216] mb-3">
                                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M3.6 9h16.8M3.6 15h16.8" />
                                    <ellipse cx="12" cy="12" rx="4.5" ry="9" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-[12.5px] font-semibold text-[#1E1E1E] leading-[1.4]">
                                {!! nl2br(e($careerHighlights?->text('stat_2_title') ?: "Competed in\nInternational\nTournaments")) !!}
                            </p>
                        </div>

                        <div class="px-3 sm:px-4 py-4 lg:py-2 flex flex-col items-center text-center">
                            <div class="h-10 flex items-center justify-center text-[#0D2216] mb-3">
                                <svg class="w-10 h-6" viewBox="0 0 48 26" fill="none" stroke="currentColor"
                                    stroke-width="1.8">
                                    <circle cx="11" cy="9.5" r="5.5" />
                                    <circle cx="24" cy="9.5" r="5.5" />
                                    <circle cx="37" cy="9.5" r="5.5" />
                                    <circle cx="17.5" cy="16.5" r="5.5" />
                                    <circle cx="30.5" cy="16.5" r="5.5" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-[12.5px] font-semibold text-[#1E1E1E] leading-[1.4]">
                                {!! nl2br(e($careerHighlights?->text('stat_3_title') ?: "Represented\nBangladesh at\nthe Olympics")) !!}
                                <br>
                                <span class="text-[11.5px] font-normal text-[#1E1E1E]/70">{{ $careerHighlights?->text('stat_3_sub') ?: '(Rio 2016)' }}</span>
                            </p>
                        </div>

                        <div class="px-3 sm:px-4 py-4 lg:py-2 flex flex-col items-center text-center">
                            <div class="h-10 flex items-center justify-center text-[#0D2216] mb-3">
                                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 3v18M6 4h11l-2.5 4L17 12H6" />
                                    <path d="M4 21h4" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-[12.5px] font-semibold text-[#1E1E1E] leading-[1.4]">
                                {!! nl2br(e($careerHighlights?->text('stat_4_title') ?: "Asian Tour\nChampion")) !!}
                                <br>
                                <span class="text-[11.5px] font-normal text-[#1E1E1E]/70">{{ $careerHighlights?->text('stat_4_sub') ?: '(2010 & 2013)' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- =====================================================================
    2. ABOUT ME (Biography, Narrative & Personal Info Grid)
    ===================================================================== --}}
    <section id="about"
        class="relative bg-cream-about text-charcoal overflow-hidden border-b border-charcoal/[0.08] pt-28 sm:pt-32 lg:pt-36 xl:pt-40 pb-8 sm:pb-10 lg:pb-12 mb-10 sm:mb-14 lg:mb-16 rounded-b-[28px] sm:rounded-b-[36px] shadow-xs">

        {{-- Left Image: Desktop --}}
        <div
            class="hidden lg:block absolute inset-y-0 left-0 w-[42%] xl:w-[40%] 2xl:w-[38%] h-full pointer-events-none select-none pt-16 sm:pt-20 lg:pt-24">
            <div class="relative w-full h-full overflow-hidden">
                <img src="{{ $biography?->imageUrl('profile_image') ?: asset('images/portfolio/Golfer-Picsart-AiImageEnhancer.webp') }}"
                    alt="Portrait of Mohammad Siddikur Rahman"
                    class="w-full h-full object-cover object-left" loading="lazy">
                <div class="absolute inset-y-0 right-0 w-28 bg-gradient-to-r from-transparent to-cream-about">
                </div>
            </div>
        </div>

        {{-- Main Container --}}
        <div class="max-w-[1320px] mx-auto w-full px-5 sm:px-8 lg:px-12 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 items-center">

                {{-- Mobile Image --}}
                <div class="lg:hidden relative min-h-[260px] sm:min-h-[300px] -mx-5 sm:-mx-8 mb-6 overflow-hidden">
                    <img src="{{ $biography?->imageUrl('profile_image') ?: asset('images/portfolio/Golfer-Picsart-AiImageEnhancer.webp') }}"
                        alt="Portrait of Mohammad Siddikur Rahman"
                        class="w-full h-full object-cover object-left" loading="lazy">
                    <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-cream-about to-transparent"></div>
                </div>

                <div class="hidden lg:block lg:col-span-5" aria-hidden="true"></div>

                {{-- Right Content --}}
                <div class="lg:col-span-7 lg:pl-6 xl:pl-10 py-1 lg:py-2 flex flex-col justify-center">
                    <div class="w-full">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="eyebrow text-charcoal/80">
                                {{ $biography?->additional_details['quick_about_eyebrow'] ?? 'ABOUT ME' }}
                            </span>
                            <span class="w-9 h-px bg-gold"></span>
                        </div>
                        <h2 class="font-serif text-3xl sm:text-[34px] lg:text-[38px] leading-[1.15] text-charcoal font-normal">
                            {{ $biography?->additional_details['quick_about_heading'] ?? 'A Passion for Golf' }}
                        </h2>
                        <div class="text-xs sm:text-[13.5px] leading-[1.75] text-charcoal/80 mt-3 space-y-2.5">
                            @if($biography?->short_introduction)
                                {!! nl2br(e($biography->short_introduction)) !!}
                            @else
                                <p>
                                    Golf has been more than just a sport for me — it’s a lifelong passion. Over the years, I’ve
                                    dedicated myself to the game, representing my country and inspiring the next generation.
                                </p>
                            @endif
                        </div>

                        <div class="mt-3.5 flex items-center gap-6">
                            <span
                                class="font-signature text-[32px] sm:text-[38px] text-charcoal/85 leading-none block select-none">
                                {{ $biography?->full_name ?: 'Siddikur Rahman' }}
                            </span>
                            <a href="{{ url('/about') }}"
                                class="inline-flex items-center gap-2 text-xs font-semibold text-charcoal hover:text-gold transition">
                                <span>Read Full Biography</span>
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>

                    {{-- Personal Info & Profile Bar --}}
                    <div
                        class="mt-4 sm:mt-5 w-full bg-white/90 backdrop-blur-sm border border-[#E8E2D5] rounded-2xl p-4 sm:p-5 shadow-[0_8px_24px_rgba(0,0,0,0.04)]">
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#E8E2D5]">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-forest"></span>
                                <h3 class="font-serif text-sm sm:text-base font-bold text-charcoal tracking-wide uppercase">
                                    Personal Info &amp; Profile
                                </h3>
                            </div>
                            <span
                                class="text-[11px] font-semibold text-charcoal/60 bg-[#FAF8F5] px-2.5 py-0.5 rounded-full border border-[#E8E2D5]">
                                {{ $info['tour_status'] ?? 'Asian Tour Member' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-3.5 text-xs">
                            {{-- Full Name --}}
                            <div class="p-2.5 rounded-xl bg-[#FAF8F5] border border-[#EFECE6] flex flex-col justify-between">
                                <span class="text-[10px] uppercase tracking-wider text-charcoal/50 font-semibold mb-1">Full Name</span>
                                <span class="font-bold text-charcoal leading-snug">{{ $info['full_name'] ?? 'Mohammad Siddikur Rahman' }}</span>
                            </div>

                            {{-- Age & DOB --}}
                            <div class="p-2.5 rounded-xl bg-[#FAF8F5] border border-[#EFECE6] flex flex-col justify-between">
                                <span class="text-[10px] uppercase tracking-wider text-charcoal/50 font-semibold mb-1">Age &amp; DOB</span>
                                <span class="font-bold text-charcoal leading-snug">{{ $info['age'] ?? '32' }} Age <span class="font-normal text-charcoal/65">({{ $info['dob'] ?? '20 Nov 1984' }})</span></span>
                            </div>

                            {{-- Career Earnings --}}
                            <div class="p-2.5 rounded-xl bg-[#FAF8F5] border border-[#EFECE6] flex flex-col justify-between">
                                <span class="text-[10px] uppercase tracking-wider text-charcoal/50 font-semibold mb-1">Career Earnings</span>
                                <span class="font-bold text-forest leading-snug">{{ $info['career_earnings'] ?? '$1,204,000+' }}</span>
                            </div>

                            {{-- Family --}}
                            <div class="p-2.5 rounded-xl bg-[#FAF8F5] border border-[#EFECE6] flex flex-col justify-between">
                                <span class="text-[10px] uppercase tracking-wider text-charcoal/50 font-semibold mb-1">Family Status</span>
                                <span class="font-bold text-charcoal leading-snug">{{ $info['family'] ?? 'Wife & Family' }}</span>
                            </div>

                            {{-- Height & Weight --}}
                            <div class="p-2.5 rounded-xl bg-[#FAF8F5] border border-[#EFECE6] flex flex-col justify-between">
                                <span class="text-[10px] uppercase tracking-wider text-charcoal/50 font-semibold mb-1">Height &amp; Weight</span>
                                <span class="font-bold text-charcoal leading-snug">{{ $info['height'] ?? '5 ft 5 in' }} • {{ $info['weight'] ?? '152 lb' }}</span>
                            </div>

                            {{-- Education --}}
                            <div class="p-2.5 rounded-xl bg-[#FAF8F5] border border-[#EFECE6] flex flex-col justify-between">
                                <span class="text-[10px] uppercase tracking-wider text-charcoal/50 font-semibold mb-1">College</span>
                                <span class="font-bold text-charcoal leading-snug">{{ $info['college'] ?? 'Dhaka College' }}</span>
                            </div>

                            {{-- Location --}}
                            <div class="p-2.5 rounded-xl bg-[#FAF8F5] border border-[#EFECE6] flex flex-col justify-between">
                                <span class="text-[10px] uppercase tracking-wider text-charcoal/50 font-semibold mb-1">Residence</span>
                                <span class="font-bold text-charcoal leading-snug">{{ $info['location'] ?? 'Dhaka, Bangladesh' }}</span>
                            </div>

                            {{-- Turned Pro --}}
                            <div class="p-2.5 rounded-xl bg-[#FAF8F5] border border-[#EFECE6] flex flex-col justify-between">
                                <span class="text-[10px] uppercase tracking-wider text-charcoal/50 font-semibold mb-1">Turned Pro</span>
                                <span class="font-bold text-charcoal leading-snug">{{ $info['turned_pro_label'] ?? ($info['turned_pro'] ?? '2005 (Asian Tour)') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- =====================================================================
    3. KEY ACHIEVEMENTS & MILESTONES
    ===================================================================== --}}
    <section id="achievements"
        class="relative text-white py-16 lg:py-24 overflow-hidden bg-[#0D2216] bg-fixed bg-cover bg-center rounded-t-[28px] sm:rounded-t-[36px] shadow-lg"
        style="background-image: url('{{ asset('images/portfolio/2-Picsart-AiImageEnhancer.png') }}');">

        <div class="absolute inset-0 pointer-events-none select-none overflow-hidden -z-0">
            <div class="absolute inset-0 bg-gradient-to-b from-[#0D2216]/80 via-[#0D2216]/50 to-[#0D2216]/85"></div>
        </div>

        <div class="max-w-[1320px] mx-auto px-5 sm:px-8 lg:px-12 relative z-10">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-2.5">
                        <span class="eyebrow text-gold font-bold">ACHIEVEMENTS</span>
                        <span class="w-10 h-px bg-gold"></span>
                    </div>
                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-[44px] leading-[1.1] text-white font-normal">
                        Key Achievements &amp; Milestones
                    </h2>
                </div>
                <p class="text-xs sm:text-sm leading-relaxed text-white/80 max-w-md">
                    A stellar career built on hard work, dedication and representing Bangladesh on the highest international
                    golf stages.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-7">
                @foreach($achievementsList as $idx => $item)
                    @php
                        $isModel = $item instanceof \App\Models\Achievement;
                        $title = $isModel ? $item->title : ($item['title'] ?? '');
                        $year = $isModel ? $item->year : ($item['year'] ?? '');
                        $badge = $isModel ? ($item->category ?: 'Champion Milestone') : ($item['badge'] ?? 'Champion Milestone');
                        $image = $isModel ? ($item->imageUrl('image') ?: asset('images/portfolio/achievement-' . (($idx % 4) + 1) . '.jpg')) : asset($item['image'] ?? 'images/portfolio/achievement-1.jpg');
                        $alt = $isModel ? ($item->image_alt ?: $title) : ($item['alt'] ?? $title);
                    @endphp
                    <article
                        class="bg-white/10 hover:bg-white/15 backdrop-blur-md rounded-[22px] border border-white/20 hover:border-gold/60 p-4 transition-all duration-300 hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(0,0,0,0.35)] flex flex-col justify-between group">
                        <div>
                            <div class="overflow-hidden rounded-[16px] aspect-[16/11] bg-black/30 relative">
                                <img src="{{ $image }}" alt="{{ $alt }}"
                                    class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-700"
                                    loading="lazy">
                                <span
                                    class="absolute top-3 left-3 bg-forest/90 backdrop-blur-sm text-gold border border-gold/40 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full">
                                    {{ $year }}
                                </span>
                            </div>

                            <div class="pt-5 pb-2">
                                <h3
                                    class="font-serif text-base sm:text-[17px] font-semibold text-white leading-snug group-hover:text-gold transition">
                                    {!! nl2br(e($title)) !!}
                                </h3>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-white/15 flex items-center justify-between text-xs text-white/70">
                            <span class="inline-flex items-center gap-1.5 text-gold font-medium">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z" />
                                </svg>
                                <span>{{ $badge }}</span>
                            </span>
                            <span class="text-white/40">#0{{ $idx + 1 }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>


    {{-- =====================================================================
    4. GALLERY (Photos & Videos)
    ===================================================================== --}}
    <section id="gallery" class="relative bg-forest-gallery text-white py-20 lg:py-24 overflow-hidden">
        <div class="max-w-[1320px] mx-auto px-5 sm:px-8 lg:px-12">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-2.5">
                        <span class="eyebrow text-white/80">MOMENTS ON THE COURSE</span>
                        <span class="w-9 h-px bg-white/35"></span>
                    </div>
                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-[44px] leading-[1.1] text-white font-normal">
                        Photo &amp; Video Gallery
                    </h2>
                </div>
                <p class="text-xs sm:text-sm text-white/75 max-w-md leading-relaxed">
                    A collection capturing intense tournament moments, pure golf swings, and historic victories across the globe.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-7">
                {{-- Dynamic Photos --}}
                @forelse($photos as $photo)
                    <div class="group relative rounded-2xl overflow-hidden aspect-[4/3] bg-black/40 border border-white/10 hover:border-gold/50 shadow-lg cursor-pointer"
                        data-gallery="{{ $photo->imageUrl('image') ?: asset('images/portfolio/gallery-1.jpg') }}"
                        data-caption="{{ $photo->caption }}">
                        <img src="{{ $photo->imageUrl('image') ?: asset('images/portfolio/gallery-1.jpg') }}"
                            alt="{{ $photo->alt_text }}"
                            class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20 opacity-80 group-hover:opacity-100 transition"></div>
                        <div class="absolute top-4 left-4">
                            <span class="bg-forest/90 border border-white/20 text-white text-[11px] font-medium px-3 py-1 rounded-full backdrop-blur-xs">Photo</span>
                        </div>
                        <div class="absolute bottom-4 inset-x-4">
                            <p class="font-serif text-base text-white font-medium">{{ $photo->caption }}</p>
                            @if($photo->photographer_credit)
                                <p class="text-[11px] text-white/70">{{ $photo->photographer_credit }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- Default Static Photos --}}
                    <div class="group relative rounded-2xl overflow-hidden aspect-[4/3] bg-black/40 border border-white/10 hover:border-gold/50 shadow-lg cursor-pointer"
                        data-gallery="{{ asset('images/portfolio/gallery-1.jpg') }}"
                        data-caption="Asian Tour Action — Siddikur Rahman mid-swing with high trajectory driver shot">
                        <img src="{{ asset('images/portfolio/gallery-1.jpg') }}" alt="Asian Tour tournament swing"
                            class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20 opacity-80 group-hover:opacity-100 transition"></div>
                        <div class="absolute top-4 left-4">
                            <span class="bg-forest/90 border border-white/20 text-white text-[11px] font-medium px-3 py-1 rounded-full backdrop-blur-xs">Photo</span>
                        </div>
                        <div class="absolute bottom-4 inset-x-4">
                            <p class="font-serif text-base text-white font-medium">Asian Tour Championship Swing</p>
                            <p class="text-[11px] text-white/70">Delhi Golf Club</p>
                        </div>
                    </div>
                @endforelse

                {{-- Dynamic Videos --}}
                @forelse($videos as $video)
                    <div class="group relative rounded-2xl overflow-hidden aspect-[4/3] bg-black/40 border border-white/10 hover:border-gold/50 shadow-lg cursor-pointer"
                        data-gallery="{{ $video->imageUrl('image') ?: asset('images/portfolio/gallery-2.jpg') }}"
                        data-caption="{{ $video->title }}">
                        <img src="{{ $video->imageUrl('image') ?: asset('images/portfolio/gallery-2.jpg') }}"
                            alt="{{ $video->title }}"
                            class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-black/30"></div>
                        <div class="absolute top-4 left-4 flex items-center gap-2">
                            <span class="bg-red-600/90 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Video
                            </span>
                            @if($video->description)
                                <span class="bg-black/60 text-white/80 text-[10px] px-2 py-0.5 rounded-full">{{ $video->description }}</span>
                            @endif
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-14 h-14 rounded-full bg-white/25 backdrop-blur-md border border-white/60 flex items-center justify-center group-hover:scale-115 group-hover:bg-gold group-hover:text-forest transition-all duration-300">
                                <svg class="w-6 h-6 fill-current translate-x-0.5" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="absolute bottom-4 inset-x-4">
                            <p class="font-serif text-base text-white font-medium">{{ $video->title }}</p>
                            @if($video->video_url)
                                <a href="{{ $video->video_url }}" target="_blank" rel="noopener" class="text-[11px] text-gold hover:underline">Watch on YouTube ↗</a>
                            @endif
                        </div>
                    </div>
                @empty
                    {{-- Default Static Video --}}
                    <div class="group relative rounded-2xl overflow-hidden aspect-[4/3] bg-black/40 border border-white/10 hover:border-gold/50 shadow-lg cursor-pointer"
                        data-gallery="{{ asset('images/portfolio/gallery-2.jpg') }}"
                        data-caption="Video Highlight: Championship Victory Celebration &amp; Trophy Presentation">
                        <img src="{{ asset('images/portfolio/gallery-2.jpg') }}" alt="Victory celebration video"
                            class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-black/30"></div>
                        <div class="absolute top-4 left-4 flex items-center gap-2">
                            <span class="bg-red-600/90 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Video
                            </span>
                            <span class="bg-black/60 text-white/80 text-[10px] px-2 py-0.5 rounded-full">01:45</span>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-14 h-14 rounded-full bg-white/25 backdrop-blur-md border border-white/60 flex items-center justify-center group-hover:scale-115 group-hover:bg-gold group-hover:text-forest transition-all duration-300">
                                <svg class="w-6 h-6 fill-current translate-x-0.5" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="absolute bottom-4 inset-x-4">
                            <p class="font-serif text-base text-white font-medium">Championship Victory Moment</p>
                            <p class="text-[11px] text-white/70">Panasonic Open India</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>


    {{-- =====================================================================
    5. COACHING AND TIPS
    ===================================================================== --}}
    <section id="coaching" class="relative bg-[#FAF8F5] text-charcoal py-20 lg:py-24 border-b border-charcoal/[0.08]">
        <div class="max-w-[1320px] mx-auto px-5 sm:px-8 lg:px-12">
            <div class="text-center mb-14">
                <h2
                    class="font-serif text-3xl sm:text-4xl lg:text-[42px] tracking-wide text-charcoal uppercase font-normal">
                    COACHING AND TIPS
                </h2>
                <div class="w-20 h-1 bg-forest mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10">
                @php
                    $coachingFallbacks = [
                        'images/portfolio/ways-to-improve-your-short-game-Picsart-AiImageEnhancer.jpg',
                        'images/portfolio/tips-for-improving-your-serve-Picsart-AiImageEnhancer.jpg',
                        'images/portfolio/wish-list-how-to-fix-a-slice-Picsart-AiImageEnhancer.jpg',
                    ];
                @endphp

                @forelse($coachingTips as $idx => $tip)
                    <article
                        class="bg-white rounded-2xl overflow-hidden border border-[#E8E2D5] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1.5 group flex flex-col">
                        <div class="overflow-hidden aspect-[16/11] bg-[#E8E4D9]">
                            <img src="{{ $tip->imageUrl('image') ?: asset($coachingFallbacks[$idx % 3]) }}"
                                alt="{{ $tip->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy">
                        </div>
                        <div class="p-6 text-center flex-1 flex flex-col justify-between">
                            <div>
                                <h3
                                    class="font-sans font-bold text-sm sm:text-base text-charcoal uppercase tracking-wide group-hover:text-forest transition">
                                    {{ $tip->title }}
                                </h3>
                                @if($tip->category)
                                    <p class="text-[11px] text-charcoal/60 uppercase tracking-widest mt-2 font-medium">
                                        {{ $tip->category }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <article
                        class="bg-white rounded-2xl overflow-hidden border border-[#E8E2D5] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1.5 group flex flex-col">
                        <div class="overflow-hidden aspect-[16/11] bg-[#E8E4D9]">
                            <img src="{{ asset('images/portfolio/ways-to-improve-your-short-game-Picsart-AiImageEnhancer.jpg') }}"
                                alt="Tips for improving your serve &amp; putt"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy">
                        </div>
                        <div class="p-6 text-center flex-1 flex flex-col justify-between">
                            <div>
                                <h3
                                    class="font-sans font-bold text-sm sm:text-base text-charcoal uppercase tracking-wide group-hover:text-forest transition">
                                    TIPS FOR IMPROVING YOUR SERVE
                                </h3>
                                <p class="text-[11px] text-charcoal/60 uppercase tracking-widest mt-2 font-medium">
                                    COACHING &amp; TIPS, TECHNICS
                                </p>
                            </div>
                        </div>
                    </article>

                    <article
                        class="bg-white rounded-2xl overflow-hidden border border-[#E8E2D5] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1.5 group flex flex-col">
                        <div class="overflow-hidden aspect-[16/11] bg-[#E8E4D9]">
                            <img src="{{ asset('images/portfolio/tips-for-improving-your-serve-Picsart-AiImageEnhancer.jpg') }}"
                                alt="Wish list: how to fix a slice"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy">
                        </div>
                        <div class="p-6 text-center flex-1 flex flex-col justify-between">
                            <div>
                                <h3
                                    class="font-sans font-bold text-sm sm:text-base text-charcoal uppercase tracking-wide group-hover:text-forest transition">
                                    WISH LIST: HOW TO FIX A SLICE
                                </h3>
                                <p class="text-[11px] text-charcoal/60 uppercase tracking-widest mt-2 font-medium">
                                    COACHING &amp; TIPS, FULL SWING, TECHNICS
                                </p>
                            </div>
                        </div>
                    </article>

                    <article
                        class="bg-white rounded-2xl overflow-hidden border border-[#E8E2D5] shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1.5 group flex flex-col">
                        <div class="overflow-hidden aspect-[16/11] bg-[#E8E4D9]">
                            <img src="{{ asset('images/portfolio/wish-list-how-to-fix-a-slice-Picsart-AiImageEnhancer.jpg') }}"
                                alt="Ways to improve your short game"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy">
                        </div>
                        <div class="p-6 text-center flex-1 flex flex-col justify-between">
                            <div>
                                <h3
                                    class="font-sans font-bold text-sm sm:text-base text-charcoal uppercase tracking-wide group-hover:text-forest transition">
                                    WAYS TO IMPROVE YOUR SHORT GAME
                                </h3>
                                <p class="text-[11px] text-charcoal/60 uppercase tracking-widest mt-2 font-medium">
                                    COACHING &amp; TIPS, SHORT GAME
                                </p>
                            </div>
                        </div>
                    </article>
                @endforelse
            </div>
        </div>
    </section>


    {{-- =====================================================================
    6. MY SCHEDULE (Tournament Calendar)
    ===================================================================== --}}
    <section id="schedule"
        class="relative text-white py-12 sm:py-16 lg:py-20 overflow-hidden bg-[#0D2216] bg-cover bg-left md:bg-center"
        style="background-image: url('{{ asset('images/portfolio/Golf-Bangladesh-Siddikur-Ra.jpg') }}');">

        <div
            class="absolute inset-0 bg-gradient-to-r from-[#0D2216]/85 via-[#0D2216]/50 to-[#0D2216]/80 pointer-events-none">
        </div>
        <div
            class="absolute inset-0 bg-gradient-to-b from-[#0D2216]/40 via-transparent to-[#0D2216]/60 pointer-events-none">
        </div>

        <div class="max-w-[1320px] mx-auto px-5 sm:px-8 lg:px-12 relative z-10">
            <div class="text-center mb-8 lg:mb-10">
                <span class="inline-block text-[11px] sm:text-xs font-bold tracking-[0.25em] text-[#A6D378] uppercase mb-2">
                    Official Tour Calendar
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl lg:text-[44px] tracking-wide text-white uppercase font-normal">
                    MY SCHEDULE
                </h2>
                <div class="w-20 h-1 bg-[#6AA438] mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="max-w-[860px] mx-auto">
                {{-- White Tournament Card --}}
                <div
                    class="bg-white/95 backdrop-blur-md rounded-[24px] sm:rounded-[28px] shadow-2xl border border-white/60 p-6 sm:p-8 lg:p-9 text-charcoal">

                    <div class="flex items-center justify-between pb-4 sm:pb-5 border-b border-[#E8E2D5]">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-[#6AA438]/10 text-[#558B2F] flex items-center justify-center shrink-0 border border-[#6AA438]/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </div>
                            <div>
                                <h3
                                    class="font-sans font-bold text-base sm:text-lg text-charcoal uppercase tracking-wider">
                                    Tournament Name And Date
                                </h3>
                                <p class="text-xs text-charcoal/55 font-medium">Championship Schedule &amp; Locations</p>
                            </div>
                        </div>
                        <span
                            class="hidden sm:inline-flex text-[11px] font-semibold text-charcoal/50 bg-[#FAF8F5] px-3 py-1 rounded-full border border-[#E8E2D5]">
                            {{ $scheduleList->count() }} Tour Events
                        </span>
                    </div>

                    {{-- Tournaments List (Scrollable to display 2-3 at a time without resizing section) --}}
                    <div class="divide-y divide-[#EFECE6] py-1 max-h-[250px] overflow-y-auto pr-1 sm:pr-2 [scrollbar-width:thin] [scrollbar-color:#6AA438_#F0EBE1]">
                        @foreach($scheduleList as $idx => $t)
                            @php
                                $isModel = $t instanceof \App\Models\Tournament;
                                $name = $isModel ? $t->name : ($t['name'] ?? '');
                                $tour = $isModel ? $t->tour : ($t['tour'] ?? '');
                                $venue = $isModel ? $t->location() : ($t['location'] ?? '');
                                $dateRange = $isModel ? $t->formattedDateRange() : ($t['date_range'] ?? '');
                            @endphp
                            <div
                                class="group flex flex-col sm:flex-row sm:items-center justify-between gap-3 py-3.5 hover:bg-[#FAF8F5] px-3 sm:px-4 rounded-xl transition-all duration-200">

                                <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-[#6AA438]/10 text-[#558B2F] flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 group-hover:bg-[#6AA438] group-hover:text-white transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h4
                                            class="font-sans font-bold text-sm sm:text-[15px] text-charcoal group-hover:text-forest transition leading-snug truncate sm:whitespace-normal">
                                            {{ $name }}
                                        </h4>
                                        @if(!empty($tour) || !empty($venue))
                                            <p class="text-[11px] text-charcoal/55 truncate mt-0.5">
                                                {{ $tour }}{{ !empty($tour) && !empty($venue) ? ' • ' : '' }}{{ $venue }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="sm:text-right shrink-0 pl-11 sm:pl-0">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#FAF8F5] text-charcoal/80 text-xs sm:text-[13px] font-semibold border border-[#E8E2D5] group-hover:bg-white group-hover:border-[#6AA438]/40 group-hover:text-forest transition shadow-2xs whitespace-nowrap">
                                        {{ $dateRange }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-5 mt-2 border-t border-[#E8E2D5] flex items-center justify-between">
                        <a href="#contact-section"
                            class="inline-flex items-center gap-2.5 px-6 py-2.5 rounded-xl bg-[#6AA438] hover:bg-[#578E29] text-white font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                            <span>Get in Touch</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2"
                                viewBox="0 0 24 24">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                        <span class="text-[11px] text-charcoal/45 italic hidden sm:inline">
                            Dates subject to official tour sanctioning
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </section>


    {{-- =====================================================================
    7. BEYOND THE COURSE (Lifestyle Banner)
    ===================================================================== --}}
    <section id="beyond"
        class="relative bg-cream-banner text-charcoal overflow-hidden min-h-[260px] lg:min-h-[290px] flex items-center border-b border-charcoal/[0.08]">
        <div class="absolute inset-y-0 right-0 w-full lg:w-[75%] pointer-events-none">
            <img src="{{ $beyond?->imageUrl('primary') ?: asset('images/portfolio/beyond-fishing.jpg') }}"
                alt="Siddikur Rahman holding a fishing rod by a scenic lake"
                class="w-full h-full object-cover object-center" loading="lazy">
            <div
                class="absolute inset-0 bg-gradient-to-r from-cream-banner via-cream-banner/85 sm:via-cream-banner/40 to-transparent">
            </div>
        </div>

        <div class="max-w-[1320px] mx-auto w-full px-5 sm:px-8 lg:px-12 py-12 lg:py-14 relative z-10">
            <div class="max-w-[280px]">
                <div class="flex items-center gap-3 mb-2">
                    <span class="eyebrow text-charcoal/80">
                        {{ $beyond?->text('eyebrow') ?: 'BEYOND THE COURSE' }}
                    </span>
                </div>
                <h2 class="font-serif text-3xl sm:text-[34px] lg:text-[36px] leading-[1.15] text-charcoal font-normal">
                    {!! nl2br(e($beyond?->text('title') ?: "Fishing &\nTravelling")) !!}
                </h2>
                <p class="text-xs sm:text-[13px] leading-[1.6] text-charcoal/80 mt-2.5">
                    {{ $beyond?->text('description') ?: 'Exploring new places, enjoying nature, and spending time by the water.' }}
                </p>
                <a href="{{ $beyond?->text('button_url') ?: '#contact-section' }}" class="mt-5 btn-dark-pill">
                    <span>{{ $beyond?->text('button_text') ?: 'Learn More' }}</span>
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>


    {{-- =====================================================================
    8. SPONSORS & PARTNERS (Marquee)
    ===================================================================== --}}
    <section id="sponsors" class="relative bg-cream text-charcoal py-14 lg:py-18 overflow-hidden">
        <div class="max-w-[1320px] mx-auto px-5 sm:px-8 lg:px-12 text-center mb-10">
            <h2 class="font-serif text-2xl sm:text-3xl lg:text-[34px] tracking-wide text-charcoal uppercase font-normal">
                SPONSORS &amp; PARTNERS
            </h2>
            <div class="w-16 h-0.5 bg-gold mx-auto mt-3"></div>
        </div>

        <div class="marquee-container py-4 border-y border-charcoal/10 bg-white/40">
            <div class="marquee-track flex items-center gap-16 lg:gap-24">
                @for($loopIndex = 0; $loopIndex < 2; $loopIndex++)
                    <div class="flex items-center gap-16 lg:gap-24 shrink-0" @if($loopIndex === 1) aria-hidden="true" @endif>
                        @forelse($sponsors as $sp)
                            <div class="text-center text-charcoal/80 hover:text-charcoal transition select-none">
                                @if($sp->imageUrl('logo'))
                                    <img src="{{ $sp->imageUrl('logo') }}" alt="{{ $sp->name }}" class="h-8 max-w-[130px] object-contain">
                                @else
                                    <span class="block font-sans font-black italic tracking-tight text-lg sm:text-xl leading-none">{{ $sp->name }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-charcoal/80 hover:text-charcoal transition select-none">
                                <span class="block font-sans font-black italic tracking-tight text-lg sm:text-xl leading-none">BRIDGESTONE</span>
                                <span class="block font-sans font-bold tracking-[0.38em] text-[10px] mt-1 text-charcoal/70">GOLF</span>
                            </div>
                            <div class="text-charcoal/80 hover:text-charcoal transition select-none">
                                <span class="font-serif italic font-bold text-2xl sm:text-3xl tracking-tight">Titleist</span>
                            </div>
                            <div class="text-charcoal/80 hover:text-charcoal transition select-none">
                                <span class="font-sans font-black text-2xl sm:text-3xl tracking-[0.14em]">PING</span>
                            </div>
                            <div class="text-center text-charcoal/80 hover:text-charcoal transition select-none">
                                <span class="font-serif font-bold text-lg sm:text-xl tracking-tight leading-none block">Callaway</span>
                            </div>
                            <div class="text-charcoal/80 hover:text-charcoal transition select-none">
                                <span class="font-sans font-bold tracking-[0.2em] text-base sm:text-lg">FOOTJOY</span>
                            </div>
                            <div class="text-charcoal/80 hover:text-charcoal transition select-none">
                                <span class="font-serif italic font-black text-xl sm:text-2xl tracking-wider">SRIXON</span>
                            </div>
                        @endforelse
                    </div>
                @endfor
            </div>
        </div>
    </section>


    {{-- =====================================================================
    9. CONTACT ME
    ===================================================================== --}}
    <section id="contact-section" class="relative bg-forest-panel text-white py-14 lg:py-16 overflow-hidden">
        <div class="pointer-events-none absolute left-2 sm:left-6 bottom-0 top-2 w-44 sm:w-56 text-[#0A180F]/65 flex items-end"
            aria-hidden="true">
            <svg viewBox="0 0 200 260" fill="currentColor" class="w-full h-[95%] object-contain">
                <path d="M35 45 C75 8, 145 12, 178 48" fill="none" stroke="currentColor" stroke-width="3.5"
                    stroke-linecap="round" />
                <ellipse cx="181" cy="51" rx="7" ry="4" transform="rotate(35 181 51)" />
                <circle cx="88" cy="58" r="14" />
                <path
                    d="M52 40 L74 72 L112 82 L100 148 L118 250 L98 250 L82 162 L72 162 L54 250 L34 248 L56 144 L64 80 L40 50 Z" />
            </svg>
        </div>

        <div class="max-w-[1320px] mx-auto px-5 sm:px-8 lg:px-12 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-5 lg:pl-24">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="eyebrow text-white/80">GET IN TOUCH</span>
                        <span class="w-8 h-px bg-white/35"></span>
                    </div>
                    <h2 class="font-serif text-3xl sm:text-[34px] lg:text-[36px] leading-[1.15] text-white font-normal">
                        Connect with Me
                    </h2>
                    <p class="text-xs sm:text-[13px] leading-[1.65] text-white/75 mt-3 max-w-[260px]">
                        For media, sponsorship or any professional inquiries, feel free to reach out.
                    </p>

                    @if($siteSettings?->contact_email)
                        <p class="text-xs text-white/80 mt-4">
                            <span class="text-gold font-medium">Email:</span> {{ $siteSettings->contact_email }}
                        </p>
                    @endif
                    @if($siteSettings?->contact_phone)
                        <p class="text-xs text-white/80 mt-1">
                            <span class="text-gold font-medium">Phone:</span> {{ $siteSettings->contact_phone }}
                        </p>
                    @endif

                    <div class="mt-6 flex items-center gap-3">
                        @if(!empty($socials['facebook']))
                            <a href="{{ $socials['facebook'] }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"
                                class="w-8 h-8 rounded-full border border-white/30 bg-white/10 hover:bg-gold hover:border-gold flex items-center justify-center transition">
                                <span class="w-5 h-5 rounded-full bg-white text-forest-panel flex items-center justify-center">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                        <path d="M13.5 22v-8.2h2.8l.4-3.2h-3.2V8.5c0-.9.3-1.6 1.6-1.6h1.7V4.1c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.3H7.3v3.2h2.8V22h3.4z" />
                                    </svg>
                                </span>
                            </a>
                        @endif
                        @if(!empty($socials['youtube']))
                            <a href="{{ $socials['youtube'] }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube"
                                class="w-8 h-8 rounded-full border border-white/30 bg-white/10 hover:bg-gold hover:border-gold flex items-center justify-center transition">
                                <span class="w-5 h-5 rounded-full bg-white text-forest-panel flex items-center justify-center">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                        <path d="M21.6 7.2a2.7 2.7 0 0 0-1.9-1.9C18 5 12 5 12 5s-6 0-7.7.3a2.7 2.7 0 0 0-1.9 1.9C2 8.9 2 12 2 12s0 3.1.4 4.8a2.7 2.7 0 0 0 1.9 1.9c1.7.3 7.7.3 7.7.3s6 0 7.7-.3a2.7 2.7 0 0 0 1.9-1.9c.4-1.7.4-4.8.4-4.8s0-3.1-.4-4.8zM10 15V9l5.2 3-5.2 3z" />
                                    </svg>
                                </span>
                            </a>
                        @endif
                        @if(!empty($socials['linkedin']))
                            <a href="{{ $socials['linkedin'] }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"
                                class="w-8 h-8 rounded-full border border-white/30 bg-white/10 hover:bg-gold hover:border-gold flex items-center justify-center transition">
                                <span class="w-5 h-5 rounded-full bg-white text-forest-panel flex items-center justify-center">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                        <path d="M6.9 8.5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM5.2 10h3.4v10.8H5.2V10zm5.5 0h3.2v1.5h.1c.5-.8 1.6-1.8 3.3-1.8 3.5 0 4.2 2.3 4.2 5.3v5.8H18v-5.1c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7v5.2h-3.6V10z" />
                                    </svg>
                                </span>
                            </a>
                        @endif
                        @if(!empty($socials['instagram']))
                            <a href="{{ $socials['instagram'] }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
                                class="w-8 h-8 rounded-full border border-white/30 bg-white/10 hover:bg-gold hover:border-gold flex items-center justify-center transition">
                                <span class="w-5 h-5 rounded-full bg-white text-forest-panel flex items-center justify-center">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                        <path d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm5 5a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm5.2-1.8a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                    </svg>
                                </span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-7">
                    @if(session('status'))
                        <div class="mb-4 rounded-[4px] border border-gold/50 bg-forest-btn px-4 py-3 text-xs text-white">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="contact-name" class="sr-only">Name</label>
                                <input id="contact-name" type="text" name="name" required placeholder="Name *"
                                    value="{{ old('name') }}"
                                    class="w-full bg-[#122117]/90 border border-white/20 rounded-[3px] px-3.5 py-2.5 text-xs text-white placeholder-white/60 focus:border-gold focus:outline-none transition">
                            </div>
                            <div>
                                <label for="contact-email" class="sr-only">Email</label>
                                <input id="contact-email" type="email" name="email" required placeholder="Email *"
                                    value="{{ old('email') }}"
                                    class="w-full bg-[#122117]/90 border border-white/20 rounded-[3px] px-3.5 py-2.5 text-xs text-white placeholder-white/60 focus:border-gold focus:outline-none transition">
                            </div>
                        </div>

                        <div>
                            <label for="contact-subject" class="sr-only">Subject</label>
                            <input id="contact-subject" type="text" name="subject" required placeholder="Subject *"
                                value="{{ old('subject') }}"
                                class="w-full bg-[#122117]/90 border border-white/20 rounded-[3px] px-3.5 py-2.5 text-xs text-white placeholder-white/60 focus:border-gold focus:outline-none transition">
                        </div>

                        <div>
                            <label for="contact-message" class="sr-only">Message</label>
                            <textarea id="contact-message" name="message" rows="3" required placeholder="Message *"
                                class="w-full bg-[#122117]/90 border border-white/20 rounded-[3px] px-3.5 py-2.5 text-xs text-white placeholder-white/60 focus:border-gold focus:outline-none transition resize-y">{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn-gold-block shadow-xs">
                            <span>Send Message</span>
                            <span aria-hidden="true">→</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
