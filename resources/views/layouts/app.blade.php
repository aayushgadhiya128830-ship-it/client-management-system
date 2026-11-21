<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Clients') | Client Management System</title>
    <link href="{{ asset('css/neumorphic.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<a href="#content" class="skip-link">Skip to main content</a>

<div class="app">

    {{-- Sidebar --}}
    <aside class="side neu-card">
        <div class="brand">
            <span class="neu-icon brand-icon"><x-icon name="users" :size="22" /></span>
            <b>Client<br>Management<br>System</b>
        </div>

        <nav aria-label="Main menu">
            <a href="{{ route('clients.index') }}"
               class="nav-item {{ request()->routeIs('clients.index', 'clients.show', 'clients.edit') ? 'on' : '' }}"
               @if (request()->routeIs('clients.index')) aria-current="page" @endif>
                <x-icon name="users" :size="20" /> Clients
            </a>
            <a href="{{ route('clients.create') }}"
               class="nav-item {{ request()->routeIs('clients.create') ? 'on' : '' }}"
               @if (request()->routeIs('clients.create')) aria-current="page" @endif>
                <x-icon name="plus" :size="20" /> Add Client
            </a>
        </nav>


        <div class="side-foot">© {{ date('Y') }} Client Management System<br>v1.0</div>
    </aside>

    {{-- Page content --}}
    <main id="content" class="main neu-card" tabindex="-1">
        @if (session('success'))
            <div class="flash flash-ok neu-inset" role="status">
                <x-icon name="check" :size="18" /> <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flash flash-bad neu-inset" role="alert">
                <x-icon name="warn" :size="18" /> <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
