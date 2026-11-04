<form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button type="submit" {{ $attributes->class(['w-full text-left px-4 py-3 rounded-lg text-sm font-medium hover:bg-red-50 hover:text-red-800 focus-visible:outline-gold']) }}>Sign out securely <span aria-hidden="true">↗</span></button>
</form>
