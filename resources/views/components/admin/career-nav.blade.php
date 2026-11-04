<nav aria-label="Biography administration" class="flex flex-wrap gap-3 mb-7 text-sm">
    <a href="{{ route('admin.biography.edit') }}" @if(request()->routeIs('admin.biography.*')) aria-current="page" @endif class="px-4 py-2 rounded-lg border border-forest/20 {{ request()->routeIs('admin.biography.*') ? 'bg-forest text-white' : 'bg-white text-forest' }}">Biography</a>
    @foreach(config('career.modules') as $slug => $definition)
        <a href="{{ route('admin.'.$slug.'.index') }}" @if(request()->routeIs('admin.'.$slug.'.*')) aria-current="page" @endif class="px-4 py-2 rounded-lg border border-forest/20 {{ request()->routeIs('admin.'.$slug.'.*') ? 'bg-forest text-white' : 'bg-white text-forest' }}">{{ $definition['label'] }}</a>
    @endforeach
</nav>
