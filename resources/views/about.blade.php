@extends('layouts.site')

@php
    $theme = $theme ?? config('theme');
    $socials = array_merge($theme['socials'] ?? [], $siteSettings?->social_links ?? []);
@endphp

@section('content')
    {{-- ================================================================= --}}
    {{-- 1. HERO SECTION: BIOGRAPHY BANNER                                 --}}
    {{-- ================================================================= --}}
    <section class="relative bg-[#0D2216] text-white pt-28 sm:pt-36 lg:pt-40 pb-16 lg:pb-20 overflow-hidden">
        {{-- Subtle background texture & decorative golf fairway gradient --}}
        <div class="absolute inset-0 pointer-events-none select-none overflow-hidden opacity-30">
            <div class="absolute inset-0 bg-gradient-to-r from-[#0D2216] via-[#0D2216]/90 to-transparent z-10"></div>
            <img src="{{ asset('images/portfolio/Golf-Bangladesh-Siddikur-Ra.jpg') }}" alt="Golf Course Background"
                class="w-full h-full object-cover object-center filter blur-xs">
        </div>

        <div class="max-w-[1320px] mx-auto px-5 sm:px-8 lg:px-12 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                {{-- Left Column: Biography Titles --}}
                <div class="lg:col-span-7">
                    <div class="flex items-center gap-3.5 mb-3.5">
                        <span class="text-xs sm:text-sm font-bold tracking-[0.28em] text-[#C5A47E] uppercase">
                            BIOGRAPHY
                        </span>
                        <span class="w-12 h-px bg-[#C5A47E]"></span>
                    </div>

                    <h1 class="font-serif text-4xl sm:text-5xl lg:text-[64px] xl:text-[70px] font-normal text-white leading-[1.08] tracking-[-0.01em]">
                        Siddikur Rahman
                    </h1>

                    <p class="text-xs sm:text-sm uppercase tracking-[0.35em] text-[#C5A47E] font-medium mt-4">
                        GOLFER &nbsp;•&nbsp; CHAMPION &nbsp;•&nbsp; INSPIRATION
                    </p>
                </div>

                {{-- Right Column: Golfer Action Photo --}}
                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                    <div class="relative w-full max-w-[420px] aspect-[4/3] sm:aspect-[16/11] lg:aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl border border-white/15">
                        <img src="{{ asset('images/portfolio/about-hero-golfer.jpg') }}"
                            alt="Mohammad Siddikur Rahman mid-swing follow through"
                            class="w-full h-full object-cover object-top"
                            loading="eager">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0D2216]/60 via-transparent to-transparent"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Bottom Gold Wave / Accent separator --}}
        <div class="absolute bottom-0 inset-x-0 h-2 bg-gradient-to-r from-transparent via-[#C5A47E]/60 to-transparent"></div>
    </section>


    {{-- ================================================================= --}}
    {{-- 2. MAIN STORY SECTION: About Siddikur Rahman                      --}}
    {{-- ================================================================= --}}
    <section class="bg-[#FAF7F0] text-[#1E1E1E] py-16 sm:py-20 lg:py-24 border-b border-[#E8E2D5]">
        <div class="max-w-[1320px] mx-auto px-5 sm:px-8 lg:px-12">

            {{-- Story Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">

                {{-- Left Column: Story text & signature --}}
                <div class="lg:col-span-7">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-8 h-[2px] bg-[#C5A47E]"></span>
                        <h2 class="font-serif text-3xl sm:text-4xl lg:text-[42px] leading-tight text-[#1E1E1E] font-normal">
                            About <span class="font-semibold">Siddikur Rahman</span>
                        </h2>
                    </div>

                    <div class="space-y-4 text-xs sm:text-[14px] leading-[1.85] text-[#1E1E1E]/85">
                        <p>
                            Worked as a ball boy at Kurmitola Golf Club in Bangladesh to support his studies. Made his first club with a broken screw into a bed sheet on a metal rod. Was picked from over 1,010 kids to train by his club and represented his country in the Asian Games. He currently attached to Kurmitola Golf Club as an honorary member.
                        </p>
                        <p>
                            Enjoyed his best result in 2017 at the Bashundhara Bangladesh Open where he finished second. Almost won his third title at the 2016 Afrasia Bank Mauritius Open where he finished second. Became the first sportsman from the country to qualify for the Olympics on merit and was accorded the honour of becoming Bangladesh's flagbearer during the opening ceremony in Rio de Janeiro. Came close to lifting his third title again at the 4th Queen's Cup and Panasonic Open India where he finished third.
                        </p>
                    </div>

                    {{-- Cursive Signature --}}
                    <div class="mt-6 pt-2">
                        <span class="font-signature text-[44px] sm:text-[52px] text-[#1E1E1E] leading-none block select-none">
                            Siddikur Rahman
                        </span>
                    </div>
                </div>

                {{-- Right Column: Featured Image with Floating Badge --}}
                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                    <div class="relative w-full max-w-[440px] rounded-2xl overflow-hidden shadow-xl border border-[#E8E2D5]">
                        <img src="{{ asset('images/portfolio/about-story-golfer.jpg') }}"
                            alt="Siddikur Rahman in full tournament swing"
                            class="w-full h-auto object-cover aspect-[4/3] sm:aspect-[16/12]"
                            loading="lazy">

                        {{-- Floating Badge: "A Proud Champion of Bangladesh" --}}
                        <div class="absolute bottom-4 left-4 right-4 sm:right-auto bg-[#0D2216]/95 backdrop-blur-md border border-[#C5A47E]/40 text-white px-4 py-2.5 rounded-xl shadow-2xl flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#C5A47E]/15 flex items-center justify-center shrink-0 text-[#C5A47E]">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-serif text-xs sm:text-[13px] font-semibold text-white leading-tight">
                                    A Proud Champion
                                </div>
                                <div class="text-[10px] text-white/70 font-sans tracking-wide">
                                    of Bangladesh
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            {{-- ============================================================= --}}
            {{-- 3. WINS & CHAMPIONSHIPS CARD (3-Column Layout)                --}}
            {{-- ============================================================= --}}
            <div class="bg-[#F3EFE6] border border-[#E4DDD0] rounded-3xl p-6 sm:p-8 lg:p-10 my-16 shadow-sm relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-stretch">

                    {{-- Column 1: Asian Tour Victories --}}
                    <div class="lg:col-span-4 flex flex-col justify-start">
                        <div class="flex items-center gap-2.5 mb-1">
                            <span class="w-6 h-[2px] bg-[#C5A47E]"></span>
                            <h3 class="font-serif text-lg sm:text-xl font-bold text-[#1E1E1E]">
                                Asian Tour Victories
                            </h3>
                        </div>
                        <p class="text-xs text-[#1E1E1E]/65 mb-5 pl-8">
                            Major Wins on the Asian Tour
                        </p>

                        <div class="space-y-3.5 pl-2">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-[#C5A47E]/15 text-[#9E7A4A] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                </span>
                                <span class="text-xs sm:text-sm font-semibold text-[#1E1E1E]">2011 Singapore Open</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-[#C5A47E]/15 text-[#9E7A4A] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                </span>
                                <span class="text-xs sm:text-sm font-semibold text-[#1E1E1E]">2013 Hero Indian Open</span>
                            </div>
                        </div>
                    </div>

                    {{-- Vertical Divider (Desktop) --}}
                    <div class="hidden lg:block lg:col-span-1 flex justify-center py-2">
                        <div class="w-px h-full bg-[#E2DCD0]"></div>
                    </div>

                    {{-- Column 2: Other Wins --}}
                    <div class="lg:col-span-4 flex flex-col justify-start">
                        <div class="flex items-center gap-2.5 mb-1">
                            <span class="w-6 h-[2px] bg-[#C5A47E]"></span>
                            <h3 class="font-serif text-lg sm:text-xl font-bold text-[#1E1E1E]">
                                Other Wins
                            </h3>
                        </div>
                        <p class="text-xs text-[#1E1E1E]/65 mb-5 pl-8">
                            Other Notable Achievements
                        </p>

                        <div class="space-y-2.5 pl-2 text-xs text-[#1E1E1E]/85">
                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 text-[#9E7A4A] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z"/></svg>
                                </span>
                                <span class="font-medium">2011 Master Invitational, Malaysia</span>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 text-[#9E7A4A] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z"/></svg>
                                </span>
                                <span class="font-medium">2010 American Express Bangladesh Open</span>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 text-[#9E7A4A] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z"/></svg>
                                </span>
                                <span class="font-medium">2009 Global Green Bangladesh Open</span>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 text-[#9E7A4A] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z"/></svg>
                                </span>
                                <span class="font-medium">2008 BDOA Open – United Haryana Open</span>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="w-5 h-5 text-[#9E7A4A] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z"/></svg>
                                </span>
                                <span class="font-medium">2008 PGTI Players Championship at Poona Golf Club</span>
                            </div>

                            <div class="flex items-start gap-3 pt-1">
                                <span class="w-5 h-5 text-[#9E7A4A] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0 0 11 15.9V18H8v2h8v-2h-3v-2.1a5.01 5.01 0 0 0 3.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2z"/></svg>
                                </span>
                                <span class="font-medium text-[#1E1E1E]/75 italic leading-snug">Five wins in Bangladesh, two each in Pakistan, Sri Lanka and Nepal, one in India</span>
                            </div>
                        </div>
                    </div>

                    {{-- Column 3: Green Champion Card --}}
                    <div class="lg:col-span-3 flex items-center justify-center">
                        <div class="w-full bg-[#0D2619] rounded-2xl p-6 sm:p-7 text-center text-white flex flex-col items-center justify-center border border-[#1B3F2B] shadow-xl">
                            {{-- Laurel Wreath & Trophy Graphic --}}
                            <div class="w-16 h-16 mb-3 text-[#C5A47E] flex items-center justify-center">
                                <svg class="w-16 h-16" viewBox="0 0 64 64" fill="none">
                                    <path d="M22 14c-4 4-6 10-5 16 1 4 4 8 8 11M42 14c4 4 6 10 5 16-1 4-4 8-8 11" stroke="#C5A47E" stroke-width="2" stroke-linecap="round"/>
                                    <circle cx="17" cy="18" r="1.5" fill="#C5A47E"/><circle cx="15" cy="24" r="1.5" fill="#C5A47E"/><circle cx="17" cy="30" r="1.5" fill="#C5A47E"/><circle cx="21" cy="36" r="1.5" fill="#C5A47E"/>
                                    <circle cx="47" cy="18" r="1.5" fill="#C5A47E"/><circle cx="49" cy="24" r="1.5" fill="#C5A47E"/><circle cx="47" cy="30" r="1.5" fill="#C5A47E"/><circle cx="43" cy="36" r="1.5" fill="#C5A47E"/>
                                    <path d="M26 22h12v7c0 3.3-2.7 6-6 6s-6-2.7-6-6v-7z" fill="#C5A47E"/>
                                    <path d="M26 24h-3c-1.1 0-2 .9-2 2v1c0 2 1.6 3.6 3.6 3.9M38 24h3c1.1 0 2 .9 2 2v1c0 2-1.6 3.6-3.6 3.9" stroke="#C5A47E" stroke-width="1.8"/>
                                    <path d="M32 35v5M28 40h8" stroke="#C5A47E" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <h4 class="font-serif text-lg font-semibold text-white tracking-wide">
                                A True Champion
                            </h4>
                            <p class="font-serif italic text-xs text-[#C5A47E] mt-0.5">
                                of Bangladesh Golf
                            </p>
                        </div>
                    </div>

                </div>
            </div>


            {{-- ============================================================= --}}
            {{-- 4. A REMARKABLE JOURNEY SECTION                               --}}
            {{-- ============================================================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center pt-4">

                {{-- Left Column: Putting Green Photo --}}
                <div class="lg:col-span-5">
                    <div class="rounded-2xl overflow-hidden shadow-lg border border-[#E8E2D5] aspect-[16/10] bg-[#E8E4D9]">
                        <img src="{{ asset('images/portfolio/about-journey-green.jpg') }}"
                            alt="Golf ball on putting green next to hole and flagstick"
                            class="w-full h-full object-cover"
                            loading="lazy">
                    </div>
                </div>

                {{-- Right Column: Remarkable Journey narrative --}}
                <div class="lg:col-span-7">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="w-8 h-[2px] bg-[#C5A47E]"></span>
                        <h2 class="font-serif text-2xl sm:text-3xl lg:text-[34px] leading-tight text-[#1E1E1E] font-normal">
                            A Remarkable Journey
                        </h2>
                    </div>

                    {{-- Callout with left gold border --}}
                    <div class="pl-4 sm:pl-5 border-l-2 border-[#C5A47E] mb-5">
                        <p class="text-xs sm:text-[13.5px] leading-[1.8] text-[#1E1E1E]/90 font-medium">
                            Siddikur became the first Bangladeshi ever to qualify for the Olympic Games after finishing 56th in the final eligibility list for the Rio 2016 Games. He previously had a wild card selection for the 2016 Olympics, when he finished 47th.
                        </p>
                    </div>

                    <p class="text-xs sm:text-[13.5px] leading-[1.8] text-[#1E1E1E]/80">
                        As a new born player from a different segment of sports, he gained more than we could think. As an amateur, Siddikur won twelve events in Asia, winning five times in Bangladesh, two in Pakistan, Sri Lanka, and Nepal and one in India.
                    </p>
                </div>

            </div>

        </div>
    </section>
@endsection

