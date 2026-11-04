<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#12372B">
    <title>{{ $title ?? 'Sign in' }} · Siddikur Rahman Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>if (window.tailwind) { tailwind.config = {theme: {extend: {colors: {forest: '#12372B', gold: '#C6A667', ivory: '#F5F3EC'}}}}; }</script>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="{{ asset('js/admin.js') }}" defer></script>
    <script src="{{ asset('js/gallery-admin.js') }}" defer></script>
</head>
<body class="bg-ivory text-slate-800 antialiased">
    <a href="#admin-main" class="skip-link">Skip to content</a>
    @yield('body')
</body>
</html>
