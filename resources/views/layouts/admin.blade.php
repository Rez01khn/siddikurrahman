@extends('layouts.admin-base')

@section('body')
    <button id="sidebar-backdrop" class="fixed inset-0 z-40 bg-black/50 lg:hidden" aria-label="Close navigation" hidden></button>
    <x-admin.sidebar />
    <div id="admin-content-wrapper" class="lg:pl-72 min-h-screen transition-all duration-300">
        <x-admin.topbar :title="$title" />
        <main id="admin-main" class="p-5 sm:p-8 lg:p-10 max-w-[1600px] mx-auto">
            @yield('content')
        </main>
        <footer class="px-5 sm:px-8 lg:px-10 py-6 border-t border-forest/10 text-xs text-slate-500">Siddikur Rahman · Website administration</footer>
    </div>
@endsection
