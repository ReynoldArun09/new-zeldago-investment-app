<header style="background-color: var(--theme-sidebar);" class="h-14 shrink-0 border-b border-l border-[#1e3a6e]/50 flex items-center px-4 gap-3 sticky top-0 z-10">

    {{-- Mobile hamburger --}}
    <button onclick="openSidebar()" class="lg:hidden text-white/50 hover:text-white/90 p-1 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Search trigger --}}
    <button onclick="openCommandPalette()"
        class="flex items-center gap-2 max-w-xs w-full border border-gray-200 hover:border-gray-300 bg-white rounded-lg px-3 py-1.5 text-left transition-colors group">
        <span class="text-gray-400 group-hover:text-gray-500 shrink-0">
            @include('admin.partials.icon', ['name' => 'search', 'size' => 14])
        </span>
        <span class="flex-1 text-sm text-gray-400 group-hover:text-gray-500 select-none transition-colors">Search here...</span>
        <span class="hidden sm:flex items-center gap-1">
            <kbd class="text-[10px] text-gray-400 border border-gray-200 rounded px-1 py-0.5 font-mono bg-gray-50">ctrl</kbd>
            <span class="text-gray-400 text-[10px]">+</span>
            <kbd class="text-[10px] text-gray-400 border border-gray-200 rounded px-1 py-0.5 font-mono bg-gray-50">K</kbd>
        </span>
    </button>

    {{-- Right actions --}}
    <div class="flex items-center gap-1 ml-auto">

        {{-- Globe --}}
        <a href="{{ url('/') }}" target="_blank" title="View Site" class="w-9 h-9 flex items-center justify-center text-white/40 hover:text-white/80 rounded-lg transition-colors">
            @include('admin.partials.icon', ['name' => 'globe', 'size' => 18])
        </a>

        {{-- Notifications --}}
        <div class="relative" id="notif-wrapper">
            @php
                $adminUser = Auth::guard('admin')->user();
                $unreadCount = $adminUser ? $adminUser->unreadNotifications->count() : 0;
            @endphp
            <button onclick="toggleNotif()" title="Notifications" class="relative w-9 h-9 flex items-center justify-center text-white/40 hover:text-white/80 rounded-lg transition-colors">
                @include('admin.partials.icon', ['name' => 'bell', 'size' => 18])
                @if($unreadCount > 0)
                <span class="absolute top-0 right-0 min-w-[16px] h-4 px-1 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full ring-2 ring-[#0d1e45]">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </button>
            <div id="notif-dropdown"
                class="hidden absolute right-0 top-full mt-2 w-80 border border-[#1e3a6e]/60 rounded-xl shadow-2xl shadow-black/40 overflow-hidden z-50" style="background-color: var(--theme-sidebar);">
                <div class="flex items-center justify-between px-4 py-3 border-b border-[#1e3a6e]/50">
                    <h3 class="font-semibold text-white text-sm">Notifications @if($unreadCount > 0) <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full ml-1">{{ $unreadCount }}</span> @endif</h3>
                    @if($unreadCount > 0)
                    <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="inline m-0 p-0">
                        @csrf
                        <button type="submit" class="text-xs text-[var(--theme-primary)] hover:opacity-80 font-medium">Mark all as read</button>
                    </form>
                    @endif
                </div>
                
                @if($unreadCount > 0)
                <div class="max-h-[300px] overflow-y-auto">
                    @foreach($adminUser->unreadNotifications as $notification)
                        <div class="px-4 py-3 border-b border-[#1e3a6e]/50 hover:bg-[#1e3a6e]/30 transition-colors">
                            <p class="text-sm text-white/90 font-medium">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            <p class="text-xs text-white/60 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                            <span class="text-[10px] text-white/40 mt-2 block">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
                @else
                <div class="py-10 text-center">
                    @include('admin.partials.icon', ['name' => 'bell', 'size' => 28])
                    <p class="text-sm text-white/35 mt-2">No unread notifications</p>
                </div>
                @endif
                
                <div class="border-t border-[#1e3a6e]/50 px-4 py-2.5">
                    <a href="{{ route('admin.notifications.index') }}" class="block w-full text-center text-sm text-[var(--theme-primary)] hover:opacity-80 font-medium">
                        View all notifications
                    </a>
                </div>
            </div>
        </div>

        {{-- Settings --}}
        <a href="{{ route('admin.settings.admin') }}" title="Settings" class="w-9 h-9 flex items-center justify-center text-white/40 hover:text-white/80 rounded-lg transition-colors">
            @include('admin.partials.icon', ['name' => 'wrench', 'size' => 18])
        </a>

        {{-- User dropdown --}}
        <div class="relative" id="user-wrapper">
            <button onclick="toggleUserMenu()" class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-lg text-white/80 hover:text-white hover:bg-white/5 transition-all">
                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" style="background-color: var(--theme-primary); box-shadow: 0 0 0 2px color-mix(in srgb, var(--theme-primary) 30%, transparent);">
                    <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <span class="hidden sm:block text-sm font-medium">{{ Auth::guard('admin')->user()->name ?? 'Admin' }}</span>
                <svg id="user-chevron" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white/40 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="user-dropdown"
                class="hidden absolute right-0 top-full mt-2 w-48 border border-[#1e3a6e]/60 rounded-xl shadow-2xl shadow-black/40 py-1.5 z-50" style="background-color: var(--theme-sidebar);">
                <a href="{{ route('admin.profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-white/70 hover:text-white hover:bg-white/5 transition-colors">
                    @include('admin.partials.icon', ['name' => 'user', 'size' => 14])
                    Profile
                </a>
                <a href="{{ route('admin.password') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-white/70 hover:text-white hover:bg-white/5 transition-colors">
                    @include('admin.partials.icon', ['name' => 'key-round', 'size' => 14])
                    Password
                </a>
                <div class="my-1 border-t border-[#1e3a6e]/50"></div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/5 transition-colors text-left">
                        @include('admin.partials.icon', ['name' => 'log-out', 'size' => 14])
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleNotif() {
        document.getElementById('notif-dropdown').classList.toggle('hidden');
        document.getElementById('user-dropdown').classList.add('hidden');
    }
    function toggleUserMenu() {
        const dd = document.getElementById('user-dropdown');
        const chevron = document.getElementById('user-chevron');
        const hidden = dd.classList.toggle('hidden');
        chevron.style.transform = hidden ? '' : 'rotate(180deg)';
        document.getElementById('notif-dropdown').classList.add('hidden');
    }
    // Close on outside click
    document.addEventListener('click', function (e) {
        if (!document.getElementById('notif-wrapper').contains(e.target)) {
            document.getElementById('notif-dropdown').classList.add('hidden');
        }
        if (!document.getElementById('user-wrapper').contains(e.target)) {
            document.getElementById('user-dropdown').classList.add('hidden');
            document.getElementById('user-chevron').style.transform = '';
        }
    });
    // Ctrl+K
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            openCommandPalette();
        }
    });
</script>
