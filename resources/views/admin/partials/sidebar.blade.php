<aside id="admin-sidebar" style="background-color: var(--theme-sidebar);"
    class="fixed top-0 left-0 h-full w-64 z-30 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:static lg:z-auto">

    {{-- Logo --}}
    <div class="flex items-center justify-between px-5 h-14 shrink-0 border-b border-[#1e3a6e]/50">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 select-none">
            <div class="flex items-center gap-2.5">
                @if(!empty($branding['logo']))
                    <img src="{{ $branding['logo'] }}" alt="{{ $theme['appName'] }}" class="w-8 h-8 rounded object-contain">
                    <span class="text-white font-bold text-base tracking-wide truncate max-w-[140px]">{{ $theme['appName'] }}</span>
                @else
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--theme-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-base tracking-wide">{{ $theme['appName'] }}</span>
                @endif
            </div>
        </a>
        <button id="sidebar-close-btn" class="lg:hidden text-white/40 hover:text-white/80 transition-colors p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-0.5">
        @foreach(admin_sidebar_menu() as $item)
            @if(isset($item['children']))
                @php $isExpanded = false; @endphp
                <div class="sidebar-group" data-label="{{ $item['label'] }}">
                    <button type="button"
                        class="sidebar-toggle w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-white/70 hover:text-white hover:bg-white/5 transition-all">
                        @include('admin.partials.icon', ['name' => $item['icon'], 'size' => 17])
                        <span class="flex-1 text-left font-medium">{{ $item['label'] }}</span>
                        <svg class="sidebar-chevron w-3 h-3 shrink-0 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="sidebar-children max-h-0 overflow-hidden transition-all duration-200 opacity-0">
                        <div class="mt-0.5 ml-3 pl-4 border-l border-[#1e3a6e]/60 space-y-0.5 py-1">
                            @foreach($item['children'] as $child)
                                @php
                                    $childUrl = url($child['href']);
                                    $childPath = ltrim(parse_url($child['href'], PHP_URL_PATH) ?? $child['href'], '/');
                                    $childQuery = parse_url($child['href'], PHP_URL_QUERY);
                                    
                                    if ($childQuery) {
                                        $childActive = request()->fullUrl() === $childUrl;
                                    } else {
                                        $childActive = request()->is($childPath) || request()->is($childPath . '/*');
                                        
                                        // Prevent 'All Users' from being active when on 'investors' or 'agents'
                                        if ($childPath === 'admin/users' && (request()->is('admin/users/investors') || request()->is('admin/users/agents'))) {
                                            $childActive = false;
                                        }

                                        // Prevent 'All ROI' from being active when on 'pending' or 'processing'
                                        if ($childPath === 'admin/roi' && (request()->is('admin/roi/pending') || request()->is('admin/roi/processing'))) {
                                            $childActive = false;
                                        }

                                        // Prevent 'All Withdrawals' from being active when on 'pending'
                                        if ($childPath === 'admin/withdrawals' && request()->is('admin/withdrawals/pending')) {
                                            $childActive = false;
                                        }
                                        
                                        if (request()->has('role')) {
                                            $childActive = false;
                                        }
                                    }
                                @endphp
                                <a href="{{ $child['href'] }}"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs text-white/55 hover:text-white hover:bg-white/5 transition-all {{ $childActive ? '!text-white font-semibold' : '' }}"
                                    @if($childActive) style="background-color: color-mix(in srgb, var(--theme-primary) 40%, transparent);" @endif>
                                    @include('admin.partials.icon', ['name' => $child['icon'] ?? 'list', 'size' => 14])
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                @php
                    $itemPath = ltrim(parse_url($item['href'], PHP_URL_PATH) ?? $item['href'], '/');
                    $itemActive = request()->path() === $itemPath;
                @endphp
                <a href="{{ $item['href'] }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ $itemActive ? 'text-white shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/5' }}"
                    @if($itemActive) style="background-color: var(--theme-primary);" @endif>
                    @include('admin.partials.icon', ['name' => $item['icon'], 'size' => 17])
                    <span class="flex-1">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t border-[#1e3a6e]/50">
        <p class="text-[10px] text-white/25 uppercase tracking-widest text-center font-semibold">{{ $theme['appName'] }} v1.0</p>
    </div>
</aside>

{{-- Mobile overlay --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-20 hidden lg:hidden" onclick="closeSidebar()"></div>

<script>
    const sidebar = document.getElementById('admin-sidebar');
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

    document.getElementById('sidebar-close-btn').addEventListener('click', closeSidebar);

    // Accordion toggles
    document.querySelectorAll('.sidebar-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const group = this.closest('.sidebar-group');
            const children = group.querySelector('.sidebar-children');
            const chevron = group.querySelector('.sidebar-chevron');
            const isOpen = children.style.maxHeight && children.style.maxHeight !== '0px';

            if (isOpen) {
                children.style.maxHeight = '0px';
                children.classList.add('opacity-0');
                chevron.style.transform = '';
            } else {
                children.style.maxHeight = children.scrollHeight + 'px';
                children.classList.remove('opacity-0');
                chevron.style.transform = 'rotate(180deg)';
            }
        });
    });
</script>
