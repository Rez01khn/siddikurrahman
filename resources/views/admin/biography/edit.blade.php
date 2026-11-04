@extends('layouts.admin')
@section('content')
<x-admin.career-nav />
<div class="flex flex-wrap justify-between gap-4 mb-7"><h1 class="font-serif text-3xl text-forest">The person behind the game.</h1><a href="{{ route('biography') }}" target="_blank" rel="noopener" class="text-sm underline text-forest">View biography ↗</a></div>
<x-admin.homepage-feedback />
<p class="text-sm text-slate-500 leading-7 mb-6">One official profile. All content is plain text, with paragraph breaks preserved. Leave unknown facts empty. Publishing requires a source note; publishing is your confirmation that the supplied facts have been reviewed.</p>
<form action="{{ route('admin.biography.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">@csrf @method('PUT')
<section class="bg-white border border-forest/10 rounded-xl p-5 sm:p-7 space-y-6"><h2 class="font-serif text-xl text-forest">Identity & story</h2>
<div class="grid md:grid-cols-2 gap-6"><x-admin.career-field name="full_name" label="Full name" :value="$biography->full_name" :maxlength="255" required /><x-admin.career-field name="professional_title" label="Professional title" :value="$biography->professional_title" :maxlength="255" required /></div>
<x-admin.career-field name="short_introduction" label="Short introduction (also used on the homepage)" type="textarea" :value="$biography->short_introduction" :maxlength="2000" />
<x-admin.career-field name="full_biography" label="Full biography" type="textarea" :value="$biography->full_biography" :maxlength="50000" />
</section>
<section class="bg-white border border-forest/10 rounded-xl p-5 sm:p-7 space-y-6"><h2 class="font-serif text-xl text-forest">Verified professional profile</h2><div class="grid md:grid-cols-2 gap-6">
<x-admin.career-field name="date_of_birth" label="Date of birth" type="date" :value="$biography->date_of_birth?->format('Y-m-d')" />
<x-admin.career-field name="birthplace" label="Birthplace" :value="$biography->birthplace" :maxlength="255" />
<x-admin.career-field name="nationality" label="Nationality" :value="$biography->nationality" :maxlength="255" />
<x-admin.career-field name="professional_start_year" label="Professional career start year" type="number" :value="$biography->professional_start_year" />
</div><p class="text-xs text-slate-500">Legacy birthplace information conflicts between pages. Do not populate it until confirmed.</p>
@for($i = 0; $i < 12; $i++)
<details class="border border-forest/15 rounded-lg p-4"><summary class="text-sm font-medium cursor-pointer">Additional verified detail {{ $i + 1 }}</summary><div class="grid md:grid-cols-2 gap-4 mt-5"><x-admin.career-field :name="'additional_details.'.$i.'.label'" label="Label" :value="data_get($biography->additional_details, $i.'.label')" :maxlength="100" /><x-admin.career-field :name="'additional_details.'.$i.'.value'" label="Value" :value="data_get($biography->additional_details, $i.'.value')" :maxlength="255" /></div><div class="mt-4"><x-admin.career-field :name="'additional_details.'.$i.'.source_note'" label="Source / verification note" :value="data_get($biography->additional_details, $i.'.source_note')" :maxlength="1000" /></div></details>
@endfor
<p class="text-xs text-slate-500">Leave all three inputs blank to remove an additional detail.</p></section>
<section class="bg-white border border-forest/10 rounded-xl p-5 sm:p-7 space-y-6"><h2 class="font-serif text-xl text-forest">Approved photography</h2>
<x-admin.career-image :record="$biography" field="profile_image" label="Profile image" />
<x-admin.career-field name="profile_image_alt" label="Profile image description / caption" :value="$biography->profile_image_alt" :maxlength="255" />
<x-admin.career-image :record="$biography" field="banner_image" label="Biography banner" />
<x-admin.career-field name="banner_image_alt" label="Banner image description / caption" :value="$biography->banner_image_alt" :maxlength="255" />
</section>
<section class="bg-white border border-forest/10 rounded-xl p-5 sm:p-7 space-y-6"><h2 class="font-serif text-xl text-forest">SEO & publication</h2>
<x-admin.career-field name="seo_title" label="SEO title" :value="$biography->seo_title" :maxlength="160" />
<x-admin.career-field name="seo_description" label="SEO description" type="textarea" :value="$biography->seo_description" :maxlength="320" />
<x-admin.career-field name="source_note" label="Source / verification note (required to publish)" type="textarea" :value="$biography->source_note" :maxlength="2000" />
<input type="hidden" name="is_published" value="0"><label class="flex items-center gap-3 text-sm"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $biography->is_published))>Publish the reviewed biography and homepage preview</label></section>
<button class="bg-forest text-white px-7 py-3 rounded-lg text-sm font-semibold">Save biography</button>
</form>
@endsection
