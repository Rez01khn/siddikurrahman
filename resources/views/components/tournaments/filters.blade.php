@props(['admin' => false, 'results' => false])
<form method="GET" class="flex flex-wrap items-end gap-4 border border-forest/10 bg-white p-5 rounded-2xl mb-8 shadow-xs text-ink">
    <div class="flex-1 min-w-44">
        <label for="q" class="block text-xs font-semibold text-slate-600 mb-2">Search events, tours or venues</label>
        <input id="q" name="q" value="{{ request('q') }}" maxlength="100" placeholder="Search by name, tour, venue..." class="w-full border border-forest/20 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-forest focus:ring-1 focus:ring-forest">
    </div>
    <div>
        <label for="year" class="block text-xs font-semibold text-slate-600 mb-2">Year</label>
        <input id="year" type="number" name="year" value="{{ request('year') }}" placeholder="All years" min="1900" max="{{ now()->year + 20 }}" class="w-32 border border-forest/20 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-forest focus:ring-1 focus:ring-forest">
    </div>
    <div>
        <label for="status" class="block text-xs font-semibold text-slate-600 mb-2">Tournament status</label>
        <select id="status" name="status" class="border border-forest/20 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-forest bg-white">
            <option value="">All</option>
            @foreach(config('tournaments.statuses') as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    @if($results)
    <div>
        <label for="result_status" class="block text-xs font-semibold text-slate-600 mb-2">Result status</label>
        <select id="result_status" name="result_status" class="border border-forest/20 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-forest bg-white">
            <option value="">All</option>
            @foreach(config('tournaments.result_statuses') as $key => $label)
                <option value="{{ $key }}" @selected(request('result_status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    @endif
    @if($admin)
    <div>
        <label for="publication" class="block text-xs font-semibold text-slate-600 mb-2">Publication</label>
        <select id="publication" name="publication" class="border border-forest/20 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-forest bg-white">
            <option value="">All</option>
            <option value="published" @selected(request('publication') === 'published')>Published</option>
            <option value="draft" @selected(request('publication') === 'draft')>Draft</option>
        </select>
    </div>
    @endif
    <div class="flex items-center gap-2">
        <button type="submit" class="bg-forest hover:bg-forest/90 text-white rounded-xl px-6 py-2.5 text-sm font-semibold transition shadow-xs">
            Search
        </button>
        <a href="{{ url()->current() }}" class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl px-5 py-2.5 text-sm font-semibold transition shadow-xs">
            Reset
        </a>
    </div>
</form>
