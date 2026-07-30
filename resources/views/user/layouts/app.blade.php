@php
    $theme = get_setting('theme_appearance');
    $primaryColor = $theme['primaryColor'] ?? '#8b5cf6';
    $sidebarColor = $theme['sidebarColor'] ?? '#ffffff'; 
    $bgColor = $theme['bgColor'] ?? '#ffffff';
    $fontFamily = $theme['fontFamily'] ?? 'Inter, sans-serif';
    $appName = $theme['appName'] ?? 'One Planet';
    
    // Add currency
    $currency = get_setting('currency_symbol') ?? 'Rs.';

    $branding = get_setting('logo_favicon') ?? [];

    // Contrast text function
    function getContrastColor($hexcolor) {
        if(strlen($hexcolor) < 6) return '#1e293b';
        $r = hexdec(substr($hexcolor, 1, 2));
        $g = hexdec(substr($hexcolor, 3, 2));
        $b = hexdec(substr($hexcolor, 5, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        return ($yiq >= 128) ? '#1e293b' : '#f8fafc';
    }
    $sidebarTextColor = getContrastColor($sidebarColor);
    $sidebarTextMuted = ($sidebarTextColor == '#f8fafc') ? '#94a3b8' : '#475569';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ $appName }}</title>
    @if(!empty($branding['favicon']))
        <link rel="icon" type="image/x-icon" href="{{ $branding['favicon'] }}">
    @endif
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary: {{ $primaryColor }};
            --sidebar-bg: {{ $sidebarColor }};
            --sidebar-text: {{ $sidebarTextColor }};
            --sidebar-muted: {{ $sidebarTextMuted }};
            --body-bg: {{ $bgColor }};
            --font-main: {{ $fontFamily }};
        }
        
        body {
            font-family: var(--font-main);
            background-color: var(--body-bg);
            color: #334155;
        }

        .text-primary { color: var(--primary); }
        .bg-primary { background-color: var(--primary); }
        .border-primary { border-color: var(--primary); }
        .bg-sidebar { background-color: var(--sidebar-bg); }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: var(--sidebar-muted);
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .sidebar-link:hover, .sidebar-link.active {
            color: var(--primary);
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-link i {
            font-size: 1.25rem;
        }
        
        /* Custom scrollbar for a cleaner look */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="antialiased flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside id="user-sidebar" class="fixed top-0 left-0 h-full w-64 z-30 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:z-auto bg-sidebar border-r border-indigo-100/10">
        <!-- Logo -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-indigo-100/10 shrink-0">
            <div class="flex items-center gap-3">
                @if(!empty($branding['logo']))
                    <img src="{{ $branding['logo'] }}" alt="{{ $appName }}" class="w-8 h-8 rounded object-contain">
                @else
                    <div class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center font-bold text-lg shadow-md shadow-indigo-200/50">
                        {{ substr($appName, 0, 1) }}
                    </div>
                @endif
                <span class="font-bold text-lg tracking-tight" style="color: var(--sidebar-text);">{{ $appName }}</span>
            </div>
            <button onclick="closeSidebar()" class="lg:hidden text-slate-400 hover:text-slate-600 transition-colors p-1">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 space-y-1">
            <a href="{{ route('user.dashboard') }}" class="sidebar-link active">
                <i class="ph ph-squares-four"></i> Dashboard
            </a>

            <!-- Investments Submenu -->
            @if(Auth::user()->account_type !== 'Agent')
            <div x-data="{ open: false }">
                <button @click="open = !open" class="sidebar-link w-full flex justify-between items-center outline-none">
                    <div class="flex items-center gap-3">
                        <i class="ph ph-trend-up"></i> Investments
                    </div>
                    <i class="ph ph-caret-down text-xs transition-transform duration-200" :class="{'rotate-180': open}"></i>
                </button>
                <div x-show="open" x-transition.opacity style="display: none;" class="pl-[33px] py-1">
                    <div class="border-l border-indigo-200/20 space-y-1 py-1">
                        <a href="{{ route('user.investments.create') }}" class="flex items-center gap-3 text-sm font-medium hover:text-primary transition-colors pl-6 py-2 {{ request()->routeIs('user.investments.create') ? 'text-primary' : '' }}" style="{{ request()->routeIs('user.investments.create') ? '' : 'color: var(--sidebar-muted);' }}">
                            <i class="ph ph-circle text-[8px]"></i> New Investment
                        </a>
                        <a href="{{ route('user.investments.active') }}" class="flex items-center gap-3 text-sm font-medium hover:text-primary transition-colors pl-6 py-2 {{ request()->routeIs('user.investments.active') ? 'text-primary' : '' }}" style="{{ request()->routeIs('user.investments.active') ? '' : 'color: var(--sidebar-muted);' }}">
                            <i class="ph ph-circle text-[8px]"></i> My Investments
                        </a>
                        <a href="{{ route('user.investments.closed') }}" class="flex items-center gap-3 text-sm font-medium hover:text-primary transition-colors pl-6 py-2 {{ request()->routeIs('user.investments.closed') ? 'text-primary' : '' }}" style="{{ request()->routeIs('user.investments.closed') ? '' : 'color: var(--sidebar-muted);' }}">
                            <i class="ph ph-circle text-[8px]"></i> Closed Investments
                        </a>
                    </div>
                </div>
            </div>
            @endif
            <!-- My team Submenu -->
            @if(Auth::user()->account_type !== 'Normal User')
            <div x-data="{ open: {{ request()->routeIs('user.network.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sidebar-link w-full flex justify-between items-center outline-none {{ request()->routeIs('user.network.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="ph ph-users"></i> My team
                    </div>
                    <i class="ph ph-caret-down text-xs transition-transform duration-200" :class="{'rotate-180': open}"></i>
                </button>
                <div x-show="open" x-transition.opacity style="display: {{ request()->routeIs('user.network.*') ? 'block' : 'none' }};" class="pl-[33px] py-1">
                    <div class="border-l border-indigo-200/20 space-y-1 py-1">
                        <a href="{{ route('user.network.referrals') }}" class="flex items-center gap-3 text-sm font-medium hover:text-primary transition-colors pl-6 py-2 {{ request()->routeIs('user.network.referrals') ? 'text-primary' : '' }}" style="{{ request()->routeIs('user.network.referrals') ? '' : 'color: var(--sidebar-muted);' }}">
                            <i class="ph ph-users"></i> Referrals
                        </a>
                        <a href="{{ route('user.network.genealogy') }}" class="flex items-center gap-3 text-sm font-medium hover:text-primary transition-colors pl-6 py-2 {{ request()->routeIs('user.network.genealogy') ? 'text-primary' : '' }}" style="{{ request()->routeIs('user.network.genealogy') ? '' : 'color: var(--sidebar-muted);' }}">
                            <i class="ph ph-tree-structure"></i> Genealogy Tree
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Finance Submenu -->
            <div x-data="{ open: {{ request()->routeIs('user.finance.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sidebar-link w-full flex justify-between items-center outline-none {{ request()->routeIs('user.finance.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="ph ph-wallet"></i> Finance
                    </div>
                    <i class="ph ph-caret-down text-xs transition-transform duration-200" :class="{'rotate-180': open}"></i>
                </button>
                <div x-show="open" x-transition.opacity style="display: {{ request()->routeIs('user.finance.*') ? 'block' : 'none' }};" class="pl-[33px] py-1">
                    <div class="border-l border-indigo-200/20 space-y-1 py-1">
                        @if(Auth::user()->account_type === 'Agent')
                        <a href="{{ route('user.finance.transactions.commissions') }}" class="flex items-center gap-3 text-sm font-medium hover:text-primary transition-colors pl-6 py-2 {{ request()->routeIs('user.finance.transactions.commissions') ? 'text-primary' : '' }}" style="{{ request()->routeIs('user.finance.transactions.commissions') ? '' : 'color: var(--sidebar-muted);' }}">
                            <i class="ph ph-trend-up"></i> Commissions
                        </a>
                        @elseif(Auth::user()->account_type === 'Normal User')
                        <a href="{{ route('user.finance.transactions.roi') }}" class="flex items-center gap-3 text-sm font-medium hover:text-primary transition-colors pl-6 py-2 {{ request()->routeIs('user.finance.transactions.roi') ? 'text-primary' : '' }}" style="{{ request()->routeIs('user.finance.transactions.roi') ? '' : 'color: var(--sidebar-muted);' }}">
                            <i class="ph ph-chart-line-up"></i> ROI Returns
                        </a>
                        @endif
                        @if(Auth::user()->account_type === 'Agent')
                        <a href="{{ route('user.finance.withdrawals') }}" class="flex items-center gap-3 text-sm font-medium hover:text-primary transition-colors pl-6 py-2 {{ request()->routeIs('user.finance.withdrawals') ? 'text-primary' : '' }}" style="{{ request()->routeIs('user.finance.withdrawals') ? '' : 'color: var(--sidebar-muted);' }}">
                            <i class="ph ph-money"></i> Withdrawals
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Settings Submenu -->
            <div x-data="{ open: {{ request()->routeIs('user.settings.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="sidebar-link w-full flex justify-between items-center outline-none {{ request()->routeIs('user.settings.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="ph ph-gear"></i> Settings
                    </div>
                    <i class="ph ph-caret-down text-xs transition-transform duration-200" :class="{'rotate-180': open}"></i>
                </button>
                <div x-show="open" x-transition.opacity style="display: {{ request()->routeIs('user.settings.*') ? 'block' : 'none' }};" class="pl-[33px] py-1">
                    <div class="border-l border-indigo-200/20 space-y-1 py-1">
                        <a href="{{ route('user.settings.profile') }}" class="flex items-center gap-3 text-sm font-medium hover:text-primary transition-colors pl-6 py-2 {{ request()->routeIs('user.settings.profile') ? 'text-primary' : '' }}" style="{{ request()->routeIs('user.settings.profile') ? '' : 'color: var(--sidebar-muted);' }}">
                            <i class="ph ph-user"></i> Profile Settings
                        </a>
                        <a href="{{ route('user.settings.password') }}" class="flex items-center gap-3 text-sm font-medium hover:text-primary transition-colors pl-6 py-2 {{ request()->routeIs('user.settings.password') ? 'text-primary' : '' }}" style="{{ request()->routeIs('user.settings.password') ? '' : 'color: var(--sidebar-muted);' }}">
                            <i class="ph ph-key"></i> Password Reset
                        </a>
                    </div>
                </div>
            </div>

            <!-- Verification Menu -->
            <a href="{{ route('user.verification.kyc') }}" class="sidebar-link {{ request()->routeIs('user.verification.*') ? 'active' : '' }}">
                <i class="ph ph-shield-check"></i> Verification
            </a>
            
            <a href="{{ route('user.support.index') }}" class="sidebar-link {{ request()->routeIs('user.support.*') ? 'active' : '' }}">
                <i class="ph ph-headset"></i> Support Tickets
            </a>


            <a href="{{ route('user.notifications.index') }}" class="sidebar-link {{ request()->routeIs('user.notifications.*') ? 'active text-red-500' : '' }}">
                <i class="ph ph-bell {{ request()->routeIs('user.notifications.*') ? 'text-red-500' : '' }}"></i> Notifications
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </a>

            <form action="{{ route('user.logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left">
                    <i class="ph ph-sign-out"></i> Log Out
                </button>
            </form>
        </nav>
        
        <!-- Bottom User Info -->
        <div class="p-4 border-t border-indigo-100/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-primary font-bold shadow-inner overflow-hidden">
                        @if(auth()->user()->profile_image)
                            <img src="{{ Storage::url(auth()->user()->profile_image) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold truncate" style="color: var(--sidebar-text);">{{ auth()->user()->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Mobile overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-20 hidden lg:hidden" onclick="closeSidebar()"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Top Navbar -->
        <header class="h-16 flex items-center justify-between px-4 sm:px-8 bg-transparent z-10 sticky top-0 backdrop-blur-sm border-b border-indigo-100/30">
            <!-- Left: Hamburger & Search -->
            <div class="flex flex-1 items-center max-w-md">
                <button onclick="openSidebar()" class="lg:hidden text-slate-500 hover:text-primary transition-colors flex items-center justify-center w-10 h-10 rounded-full hover:bg-slate-50 mr-2 shrink-0">
                    <i class="ph ph-list text-2xl"></i>
                </button>
                <div class="relative w-full" x-data>
                    <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" readonly @click="$dispatch('open-palette')" placeholder="Search menu... (Ctrl+K)" class="w-full bg-white/50 border border-indigo-100/50 rounded-full pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:bg-white transition-all cursor-pointer">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <kbd class="hidden sm:inline-flex items-center gap-1 rounded border border-slate-200 px-1.5 font-mono text-[10px] font-medium text-slate-400">
                            <span class="text-xs">⌘</span>K
                        </kbd>
                    </div>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-6">
                <!-- Notifications -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="relative text-slate-500 hover:text-primary transition-colors flex items-center justify-center w-8 h-8 rounded-full hover:bg-slate-50">
                        <i class="ph ph-bell text-xl"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-0 right-0 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-red-500 text-[8px] font-bold text-white border border-white">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                    
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute right-0 mt-3 w-[27rem] bg-white rounded-xl shadow-xl shadow-indigo-200/50 border border-indigo-50 py-2 z-50">
                        <div class="px-4 py-2 border-b border-slate-50 flex justify-between items-center">
                            <span class="font-semibold text-sm">Notifications</span>
                            <span class="text-xs text-primary bg-indigo-50 px-2 py-0.5 rounded-full">{{ auth()->user()->unreadNotifications->count() }} new</span>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                <div class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                    <div class="flex gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center shrink-0">
                                            <i class="ph {{ $notification->data['icon'] ?? 'ph-bell' }} text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-gray-800">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                            <p class="text-[10px] text-gray-500 mt-0.5 truncate max-w-[200px]">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-[9px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-6 text-center text-slate-400 text-sm border-b border-slate-50">
                                    No notifications
                                </div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2 text-center border-t border-slate-50">
                            <a href="{{ route('user.notifications.index') }}" class="text-xs text-primary hover:underline font-medium">View all</a>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-primary font-semibold text-xs shadow-inner overflow-hidden">
                            @if(auth()->user()->profile_image)
                                <img src="{{ Storage::url(auth()->user()->profile_image) }}" alt="Profile" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            @endif
                        </div>
                        <div class="text-left hidden sm:block">
                            <p class="text-xs font-bold text-indigo-950">{{ auth()->user()->name }}</p>
                        </div>
                    </button>
                    
                    <div x-show="open" x-transition.opacity style="display: none;" class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-xl shadow-indigo-200/50 border border-indigo-50 py-1 z-50">
                        <a href="{{ route('user.settings.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-indigo-50 hover:text-primary transition-colors">
                            <i class="ph ph-user-circle text-lg"></i> Profile Setting
                        </a>
                        <a href="{{ route('user.settings.password') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-indigo-50 hover:text-primary transition-colors">
                            <i class="ph ph-key text-lg"></i> Change Password
                        </a>
                        <div class="h-px bg-slate-100 my-1"></div>
                        <form action="{{ route('user.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                <i class="ph ph-sign-out text-lg"></i> Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Area -->
        <main class="flex-1 overflow-y-auto p-8 relative z-0">
            @yield('content')
        </main>
    </div>

    <!-- Command Palette -->
    <div x-data="commandPalette()" 
         @keydown.window.ctrl.k.prevent="open = true"
         @keydown.window.meta.k.prevent="open = true"
         @open-palette.window="open = true"
         x-show="open" 
         style="display: none;" 
         class="relative z-[100]"
         aria-modal="true">
        
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"></div>
        
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 sm:p-6 md:p-20">
            <div x-show="open" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="open = false" 
                 class="mx-auto max-w-xl transform divide-y divide-slate-100 overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
                <div class="relative flex items-center px-4">
                    <i class="ph ph-magnifying-glass text-slate-400 text-xl"></i>
                    <input x-model="search" x-ref="searchInput" type="text" class="h-14 w-full border-0 bg-transparent pl-4 pr-4 text-slate-900 placeholder:text-slate-400 focus:ring-0 sm:text-sm outline-none" placeholder="Search menu...">
                    <kbd class="hidden sm:inline-flex items-center gap-1 rounded border border-slate-200 px-1.5 font-mono text-[10px] font-medium text-slate-400 cursor-pointer hover:bg-slate-50" @click="open = false">
                        ESC
                    </kbd>
                </div>

                <!-- Results -->
                <ul class="max-h-80 scroll-py-3 overflow-y-auto p-3" x-show="filteredItems.length > 0">
                    <template x-for="item in filteredItems" :key="item.url">
                        <li class="group flex cursor-pointer select-none rounded-xl p-3 hover:bg-indigo-50 transition-colors" @click="window.location.href = item.url">
                            <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-indigo-100 group-hover:bg-primary group-hover:text-white transition-colors text-primary">
                                <i :class="item.icon" class="text-lg"></i>
                            </div>
                            <div class="ml-4 flex-auto">
                                <p class="text-sm font-medium text-slate-700 group-hover:text-primary transition-colors" x-text="item.title"></p>
                                <p class="text-xs text-slate-500" x-text="item.category"></p>
                            </div>
                        </li>
                    </template>
                </ul>

                <!-- No results -->
                <div x-show="search !== '' && filteredItems.length === 0" class="px-6 py-14 text-center text-sm sm:px-14">
                    <i class="ph ph-warning-circle text-4xl text-slate-300 mb-4 inline-block"></i>
                    <p class="font-semibold text-slate-900">No results found</p>
                    <p class="mt-2 text-slate-500">We couldn't find anything matching "<span x-text="search" class="font-medium text-slate-700"></span>".</p>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        const sidebar = document.getElementById('user-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        window.openSidebar = function () {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
        };

        window.closeSidebar = function () {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('commandPalette', () => ({
                open: false,
                search: '',
                items: [
                    { title: 'Dashboard', url: '{{ route('user.dashboard') }}', category: 'General', icon: 'ph-squares-four' },
                    @if(Auth::user()->account_type !== 'Agent')
                    { title: 'New Investment', url: '{{ route('user.investments.create') }}', category: 'Investments', icon: 'ph-trend-up' },
                    { title: 'My Investments', url: '{{ route('user.investments.active') }}', category: 'Investments', icon: 'ph-trend-up' },
                    { title: 'Closed Investments', url: '{{ route('user.investments.closed') }}', category: 'Investments', icon: 'ph-trend-up' },
                    @endif
                    @if(Auth::user()->account_type !== 'Normal User')
                    { title: 'Referrals', url: '{{ route('user.network.referrals') }}', category: 'Network', icon: 'ph-users' },
                    { title: 'Genealogy Tree', url: '{{ route('user.network.genealogy') }}', category: 'Network', icon: 'ph-tree-structure' },
                    @endif
                    @if(Auth::user()->account_type === 'Agent')
                    { title: 'Commissions', url: '{{ route('user.finance.transactions.commissions') }}', category: 'Finance', icon: 'ph-trend-up' },
                    @elseif(Auth::user()->account_type === 'Normal User')
                    { title: 'ROI Returns', url: '{{ route('user.finance.transactions.roi') }}', category: 'Finance', icon: 'ph-chart-line-up' },
                    @endif
                    @if(Auth::user()->account_type === 'Agent')
                    { title: 'Withdrawals', url: '{{ route('user.finance.withdrawals') }}', category: 'Finance', icon: 'ph-money' },
                    @endif
                    { title: 'Profile Settings', url: '{{ route('user.settings.profile') }}', category: 'Settings', icon: 'ph-user' },
                    { title: 'Password Reset', url: '{{ route('user.settings.password') }}', category: 'Settings', icon: 'ph-key' },
                    { title: 'KYC Verification', url: '{{ route('user.verification.kyc') }}', category: 'Verification', icon: 'ph-shield-check' },
                    { title: 'Nominee Verification', url: '{{ route('user.verification.nominee') }}', category: 'Verification', icon: 'ph-users' },
                    { title: 'Bank Verification', url: '{{ route('user.verification.bank') }}', category: 'Verification', icon: 'ph-bank' },
                    { title: 'Support Tickets', url: '{{ route('user.support.index') }}', category: 'Support', icon: 'ph-headset' },
                    { title: 'Notifications', url: '{{ route('user.notifications.index') }}', category: 'General', icon: 'ph-bell' },
                ],
                get filteredItems() {
                    if (this.search === '') {
                        return this.items;
                    }
                    return this.items.filter(item => 
                        item.title.toLowerCase().includes(this.search.toLowerCase()) || 
                        item.category.toLowerCase().includes(this.search.toLowerCase())
                    );
                },
                init() {
                    this.$watch('open', value => {
                        if (value) {
                            setTimeout(() => this.$refs.searchInput.focus(), 50);
                        } else {
                            this.search = '';
                        }
                    });
                }
            }));
        });
    </script>
</body>
</html>
