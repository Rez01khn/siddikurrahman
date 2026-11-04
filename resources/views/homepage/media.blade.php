<section id="media" class="bg-[#e6e5db] section-space">
    <div class="shell reveal"><div class="flex flex-wrap justify-between items-end gap-5"><x-section-heading :eyebrow="$section->text('eyebrow')" :title="$section->text('title')"/><p class="text-sm text-ink/75 mb-10">{{ $galleryPhotos->isNotEmpty() ? 'Photo collection · select a photograph to explore' : $section->text('description') }}</p></div>
        <div class="grid md:grid-cols-3 gap-4">
            @if($galleryPhotos->isNotEmpty())
            @foreach($galleryPhotos as $photo)
                @if($photo->imageUrl())<button data-gallery="{{ $photo->imageUrl() }}" data-caption="{{ implode(' · ',array_filter([$photo->caption ?: $photo->alt_text,$photo->photographer_credit])) }}" class="image-wrap relative {{ $loop->first ? 'md:col-span-2 md:row-span-2' : '' }}"><img src="{{ $photo->imageUrl() }}" alt="{{ $photo->alt_text }}" loading="lazy" class="w-full object-cover {{ $loop->first ? 'h-[400px] md:h-[516px]' : 'h-[250px]' }}"><span class="absolute bottom-4 left-4 bg-forest/90 text-white px-4 py-3 text-sm">{{ $photo->caption ?: 'View photograph' }} ↗</span></button>@endif
            @endforeach
            @else
            <button data-gallery="{{ $section->imageUrl() }}" data-caption="{{ $section->text('image_caption') }}" class="image-wrap relative md:col-span-2 md:row-span-2 text-left group"><img src="{{ $section->imageUrl() }}" alt="{{ $section->text('image_alt') }}" loading="lazy" class="h-[400px] md:h-[516px] w-full object-cover"><span class="absolute bottom-5 left-5 bg-forest/90 text-white px-4 py-3 text-sm">{{ $section->text('photo_label') }} <span class="ml-10">↗</span></span></button>
            <button data-gallery="{{ $section->imageUrl('secondary') }}" data-caption="{{ $section->text('secondary_image_caption') }}" class="image-wrap relative"><img src="{{ $section->imageUrl('secondary') }}" alt="{{ $section->text('secondary_image_alt') }}" loading="lazy" class="h-[250px] w-full object-cover"><span class="absolute bottom-4 right-4 bg-forest text-white w-9 h-9 grid place-items-center">↗</span></button>
            @endif
            <div class="bg-forest p-7 text-white min-h-[250px] flex flex-col justify-between"><h3 id="videos" class="eyebrow text-gold">{{ $section->text('video_label') }}</h3>@if($galleryVideo)<div><h3 class="font-display text-4xl uppercase">{{ $galleryVideo->title }}</h3><a href="{{ route('videos.show',$galleryVideo->slug) }}" class="inline-block mt-6 text-sm text-gold underline">Watch video ↗</a></div>@else<div><span class="font-display text-4xl uppercase"><x-homepage.lines :text="$section->text('video_heading')" /></span><p class="text-sm text-white/75 mt-4"><x-homepage.lines :text="$section->text('video_description')" /></p></div>@endif</div>
        </div>
        <div class="flex flex-wrap gap-7 mt-8 text-sm text-forest"><a class="underline" href="{{ route('photos.index') }}">Browse photo albums ↗</a><a class="underline" href="{{ route('videos.index') }}">Browse videos ↗</a></div>
    </div>
</section>
