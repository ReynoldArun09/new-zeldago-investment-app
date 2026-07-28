@php
    $theme = get_setting('theme_appearance') ?? [
        'sidebarColor' => '#0d1e45',
        'primaryColor' => '#3b5fc0',
        'bgColor'      => '#ffffff',
        'fontFamily'   => 'Inter, sans-serif',
        'appName'      => 'MLM Admin'
    ];
    $branding = get_setting('logo_favicon') ?? [];
@endphp
<!DOCTYPE html>
<html lang="en" style="--theme-sidebar: {{ $theme['sidebarColor'] }}; --theme-primary: {{ $theme['primaryColor'] }}; --theme-bg: {{ $theme['bgColor'] }}; font-family: {{ $theme['fontFamily'] }};">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - {{ $theme['appName'] }}</title>
    @if(!empty($branding['favicon']))
        <link rel="icon" type="image/x-icon" href="{{ $branding['favicon'] }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @stack('styles')
</head>
<body class="text-gray-800 antialiased" style="background-color: var(--theme-bg);">

    <div class="flex h-screen overflow-hidden" style="background-color: var(--theme-bg);">

        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        {{-- Main column --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            @include('admin.partials.topbar')

            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Command Palette --}}
    @include('admin.partials.command-palette')

    @stack('scripts')
</body>
</html>
