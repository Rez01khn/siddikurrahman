@extends('layouts.admin')
@section('content')
<div class="flex flex-wrap justify-between gap-4 mb-7">
    <div>
        <h1 class="font-serif text-3xl text-forest">Biography & Story</h1>
        <p class="text-sm text-slate-500 mt-1">Manage personal profile, stories, and victories displayed on the About page and Homepage.</p>
    </div>
    <div class="flex items-center gap-3 self-start">
        <a href="{{ route('home') }}#about" target="_blank" rel="noopener" class="text-sm underline text-forest">View on Home ↗</a>
        <a href="{{ route('about') }}" target="_blank" rel="noopener" class="text-sm underline text-forest">View About Page ↗</a>
    </div>
</div>

<x-admin.homepage-feedback />

<form action="{{ route('admin.biography.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')

    {{-- 1. Identity & Narrative --}}
    <section class="bg-white border border-forest/10 rounded-xl p-6 space-y-6 shadow-xs">
        <h2 class="font-serif text-xl text-forest">Identity & Narrative</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <x-admin.career-field name="full_name" label="Full Name" :value="$biography->full_name ?: 'Mohammad Siddikur Rahman'" :maxlength="255" required />
            <x-admin.career-field name="professional_title" label="Professional Title / Tagline" :value="$biography->professional_title ?: 'GOLFER • CHAMPION • INSPIRATION'" :maxlength="255" required />
        </div>
        <x-admin.career-field name="short_introduction" label="Short Introduction (Homepage Quick About)" type="textarea" :value="$biography->short_introduction" :rows="4" :maxlength="2000" />
        <x-admin.career-field name="full_biography" label="Full Story Narrative (About Page Main Story)" type="textarea" :value="$biography->full_biography" :rows="8" :maxlength="50000" />
    </section>

    {{-- 2. Personal Profile & Stats (Home & About) --}}
    <section class="bg-white border border-forest/10 rounded-xl p-6 space-y-6 shadow-xs">
        <h2 class="font-serif text-xl text-forest">Profile & Stats Details</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-admin.career-field name="date_of_birth" label="Date of Birth" type="date" :value="$biography->date_of_birth?->format('Y-m-d') ?: '1984-11-20'" />
            <x-admin.career-field name="birthplace" label="Residence / Location" :value="$biography->birthplace ?: 'Dhaka, Bangladesh'" :maxlength="255" />
            <x-admin.career-field name="nationality" label="Nationality" :value="$biography->nationality ?: 'Bangladeshi'" :maxlength="255" />
            <x-admin.career-field name="professional_start_year" label="Turned Pro Year" type="number" :value="$biography->professional_start_year ?: 2005" />
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 pt-3 border-t border-forest/10">
            <x-admin.career-field name="additional_details[profile_info][age]" label="Age" :value="data_get($biography->additional_details, 'profile_info.age', '32')" />
            <x-admin.career-field name="additional_details[profile_info][career_earnings]" label="Career Earnings" :value="data_get($biography->additional_details, 'profile_info.career_earnings', '$1,204,000+')" />
            <x-admin.career-field name="additional_details[profile_info][family]" label="Family Status" :value="data_get($biography->additional_details, 'profile_info.family', 'Wife & Family')" />
            <x-admin.career-field name="additional_details[profile_info][height]" label="Height" :value="data_get($biography->additional_details, 'profile_info.height', '5 ft 5 in')" />
            <x-admin.career-field name="additional_details[profile_info][weight]" label="Weight" :value="data_get($biography->additional_details, 'profile_info.weight', '152 lb')" />
            <x-admin.career-field name="additional_details[profile_info][college]" label="College / Education" :value="data_get($biography->additional_details, 'profile_info.college', 'Dhaka College')" />
            <x-admin.career-field name="additional_details[profile_info][tour_status]" label="Tour Status" :value="data_get($biography->additional_details, 'profile_info.tour_status', 'Asian Tour Member')" />
            <x-admin.career-field name="additional_details[profile_info][turned_pro_label]" label="Turned Pro Details" :value="data_get($biography->additional_details, 'profile_info.turned_pro_label', '2005 (Asian Tour)')" />
        </div>
    </section>

    {{-- 3. Wins & Championships (About Page) --}}
    <section class="bg-white border border-forest/10 rounded-xl p-6 space-y-6 shadow-xs">
        <h2 class="font-serif text-xl text-forest">Wins & Championships (About Page Card)</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <x-admin.career-field name="additional_details[asian_tour_victories]" label="Major Asian Tour Victories (One per line)" type="textarea" :rows="5" :value="data_get($biography->additional_details, 'asian_tour_victories', \"2011 Singapore Open\n2013 Hero Indian Open\")" />
            </div>
            <div>
                <x-admin.career-field name="additional_details[other_wins]" label="Other Notable Wins & Achievements (One per line)" type="textarea" :rows="5" :value="data_get($biography->additional_details, 'other_wins', \"2011 Master Invitational, Malaysia\n2010 American Express Bangladesh Open\n2009 Global Green Bangladesh Open\n2008 BDOA Open – United Haryana Open\n2008 PGTI Players Championship at Poona Golf Club\")" />
            </div>
        </div>
        <x-admin.career-field name="additional_details[wins_summary]" label="Summary Note" :value="data_get($biography->additional_details, 'wins_summary', 'Five wins in Bangladesh, two each in Pakistan, Sri Lanka and Nepal, one in India')" />
    </section>

    {{-- 4. Remarkable Journey (About Page Callout) --}}
    <section class="bg-white border border-forest/10 rounded-xl p-6 space-y-6 shadow-xs">
        <h2 class="font-serif text-xl text-forest">A Remarkable Journey (About Page Bottom Section)</h2>
        <x-admin.career-field name="additional_details[remarkable_journey_quote]" label="Olympics / Qualification Highlight Quote" type="textarea" :rows="3" :value="data_get($biography->additional_details, 'remarkable_journey_quote', 'Siddikur became the first Bangladeshi ever to qualify for the Olympic Games after finishing 56th in the final eligibility list for the Rio 2016 Games. He previously had a wild card selection for the 2016 Olympics, when he finished 47th.')" />
        <x-admin.career-field name="additional_details[remarkable_journey_narrative]" label="Journey Narrative Text" type="textarea" :rows="3" :value="data_get($biography->additional_details, 'remarkable_journey_narrative', 'As a new born player from a different segment of sports, he gained more than we could think. As an amateur, Siddikur won twelve events in Asia, winning five times in Bangladesh, two in Pakistan, Sri Lanka, and Nepal and one in India.')" />
    </section>

    {{-- 5. Approved Photography --}}
    <section class="bg-white border border-forest/10 rounded-xl p-6 space-y-6 shadow-xs">
        <h2 class="font-serif text-xl text-forest">Story & Banner Photography</h2>
        <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <x-admin.career-image :record="$biography" field="profile_image" label="Story Portrait Image (About Story & Homepage About)" />
                <x-admin.career-field name="profile_image_alt" label="Alt text / Caption" :value="$biography->profile_image_alt" :maxlength="255" />
            </div>
            <div class="space-y-4">
                <x-admin.career-image :record="$biography" field="banner_image" label="Action Hero Photo (About Page Top Banner)" />
                <x-admin.career-field name="banner_image_alt" label="Alt text / Caption" :value="$biography->banner_image_alt" :maxlength="255" />
            </div>
        </div>
    </section>

    {{-- 6. SEO & Publication --}}
    <section class="bg-white border border-forest/10 rounded-xl p-6 space-y-6 shadow-xs">
        <h2 class="font-serif text-xl text-forest">SEO & Publication</h2>
        <x-admin.career-field name="seo_title" label="SEO Title" :value="$biography->seo_title" :maxlength="160" />
        <x-admin.career-field name="seo_description" label="SEO Description" type="textarea" :value="$biography->seo_description" :maxlength="320" />
        <input type="hidden" name="is_published" value="0">
        <label class="flex items-center gap-3 text-sm font-medium text-slate-700 cursor-pointer">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $biography->is_published ?? true))>
            <span>Publish biography content on the website</span>
        </label>
    </section>

    <div class="flex items-center gap-4">
        <button type="submit" class="bg-forest text-white px-8 py-3.5 rounded-lg text-sm font-semibold hover:bg-forest/90">Save Biography</button>
    </div>
</form>
@endsection
