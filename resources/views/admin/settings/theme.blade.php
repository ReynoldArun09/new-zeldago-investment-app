@extends('admin.layouts.app')

@section('title', 'Theme & Appearance')

@section('content')
@php
    $theme = get_setting('theme_appearance') ?? [
        'sidebarColor' => '#0d1e45',
        'primaryColor' => '#3b5fc0',
        'bgColor'      => '#ffffff',
        'fontFamily'   => 'Inter, sans-serif',
        'appName'      => 'MLM Admin'
    ];
@endphp
<div class="w-full">
    {{-- Header --}}
    <form method="POST" action="{{ route('admin.settings.theme.update') }}">
        @csrf
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-bold text-gray-800">Theme & Appearance</h1>
                <p class="text-xs text-gray-500 mt-0.5">Customize the look and feel of your admin panel.</p>
            </div>
            <div class="flex gap-2">
                <a href="" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5"/>
                    </svg>
                    Reset
                </a>
                <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm text-white rounded-lg transition-all" style="background-color: var(--theme-primary);">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Left column --}}
            <div class="lg:col-span-2 space-y-3">
                
                {{-- App Branding --}}
                <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                    <h2 class="text-sm font-semibold text-gray-800 mb-2">App Branding</h2>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1.5">App Name</label>
                        <input type="text" name="appName" value="{{ $theme['appName'] ?? '' }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-800 outline-none focus:border-[#3b5fc0] transition-colors">
                    </div>
                </div>

                {{-- Color Presets --}}
                <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center bg-blue-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#3b5fc0]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-800">Color Presets</h2>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        @php
                            $presets = [
                                ['label' => 'Navy Blue',   'sidebar' => '#0d1e45', 'primary' => '#3b5fc0'],
                                ['label' => 'Dark Slate',  'sidebar' => '#1a1a2e', 'primary' => '#7c3aed'],
                                ['label' => 'Deep Green',  'sidebar' => '#0f2820', 'primary' => '#059669'],
                                ['label' => 'Charcoal',    'sidebar' => '#1c1c1e', 'primary' => '#f97316'],
                                ['label' => 'Burgundy',    'sidebar' => '#2d1b1b', 'primary' => '#dc2626'],
                                ['label' => 'Midnight',    'sidebar' => '#13111c', 'primary' => '#a855f7'],
                            ];
                        @endphp
                        @foreach($presets as $preset)
                            @php
                                $isActive = ($theme['sidebarColor'] === $preset['sidebar'] && $theme['primaryColor'] === $preset['primary']);
                            @endphp
                            <button type="button" 
                                onclick="applyPreset('{{ $preset['sidebar'] }}', '{{ $preset['primary'] }}')"
                                class="preset-btn flex items-center gap-2 p-2 rounded-lg border transition-all text-left {{ $isActive ? 'border-[#3b5fc0] bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <div class="flex shrink-0">
                                    <div class="w-4 h-4 rounded-l" style="background-color: {{ $preset['sidebar'] }}"></div>
                                    <div class="w-4 h-4 rounded-r" style="background-color: {{ $preset['primary'] }}"></div>
                                </div>
                                <span class="text-[10px] font-medium text-gray-700 truncate">{{ $preset['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Custom Colors --}}
                <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center bg-blue-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#3b5fc0]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-800">Custom Colors</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-2">Sidebar Color</label>
                            <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-2.5 py-1.5">
                                <input type="color" id="input-sidebarColor" name="sidebarColor" value="{{ $theme['sidebarColor'] ?? '#0d1e45' }}"
                                    oninput="updateColors()" class="w-6 h-6 rounded cursor-pointer border-none bg-transparent">
                                <span id="val-sidebarColor" class="text-xs font-mono text-gray-500 uppercase">{{ $theme['sidebarColor'] }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-2">Primary Color</label>
                            <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-2.5 py-1.5">
                                <input type="color" id="input-primaryColor" name="primaryColor" value="{{ $theme['primaryColor'] ?? '#3b5fc0' }}"
                                    oninput="updateColors()" class="w-6 h-6 rounded cursor-pointer border-none bg-transparent">
                                <span id="val-primaryColor" class="text-xs font-mono text-gray-500 uppercase">{{ $theme['primaryColor'] }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-2">Background</label>
                            <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-2.5 py-1.5">
                                <input type="color" id="input-bgColor" name="bgColor" value="{{ $theme['bgColor'] ?? '#ffffff' }}"
                                    oninput="updateColors()" class="w-6 h-6 rounded cursor-pointer border-none bg-transparent">
                                <span id="val-bgColor" class="text-xs font-mono text-gray-500 uppercase">{{ $theme['bgColor'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Typography --}}
                <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center bg-blue-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#3b5fc0]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </div>
                        <h2 class="text-sm font-semibold text-gray-800">Typography</h2>
                    </div>
                    <input type="hidden" name="fontFamily" id="input-fontFamily" value="{{ $theme['fontFamily'] ?? 'Inter, sans-serif' }}">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @php
                            $fonts = [
                                ['label' => 'Inter', 'value' => 'Inter, sans-serif'],
                                ['label' => 'Roboto', 'value' => 'Roboto, sans-serif'],
                                ['label' => 'Outfit', 'value' => 'Outfit, sans-serif'],
                                ['label' => 'Poppins', 'value' => 'Poppins, sans-serif'],
                                ['label' => 'DM Sans', 'value' => '\'DM Sans\', sans-serif'],
                                ['label' => 'Nunito', 'value' => 'Nunito, sans-serif'],
                            ];
                        @endphp
                        @foreach($fonts as $font)
                            @php
                                $isActive = ($theme['fontFamily'] === $font['value']);
                            @endphp
                            <button type="button" 
                                onclick="selectFont(this, '{{ $font['value'] }}')"
                                style="font-family: {{ $font['value'] }}"
                                class="font-btn px-3 py-2 rounded-lg border text-xs font-medium transition-all {{ $isActive ? 'border-[#3b5fc0] bg-blue-50 text-[#2a4aad]' : 'border-gray-200 text-gray-700 hover:border-gray-300' }}">
                                {{ $font['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Column: Live Preview --}}
            <div class="lg:col-span-1">
                <div class="sticky top-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3">Live Preview</p>
                    <div id="preview-box" class="rounded-xl overflow-hidden border border-gray-200 shadow-md bg-white">
                        <div class="flex h-52">
                            {{-- Preview Sidebar --}}
                            <div id="preview-sidebar" class="w-28 flex flex-col py-3 px-2 gap-1 shrink-0" style="background-color: {{ $theme['sidebarColor'] }}">
                                <p id="preview-appName" class="text-[9px] font-bold px-2 pb-2 mb-1 border-b border-white/10 text-white">{{ $theme['appName'] }}</p>
                                <div class="flex items-center gap-1.5 px-2 py-1.5 rounded text-[8px] font-medium text-white" style="background-color: {{ $theme['primaryColor'] }}">
                                    Dashboard
                                </div>
                                <div class="text-white/50 text-[8px] px-2 py-1">Investments</div>
                                <div class="text-white/50 text-[8px] px-2 py-1">Settings</div>
                            </div>
                            {{-- Preview Content --}}
                            <div id="preview-content" class="flex-1 flex flex-col" style="background-color: {{ $theme['bgColor'] }}">
                                <div class="h-7 border-b border-gray-100 flex items-center px-2 bg-gray-50">
                                    <div class="flex-1 h-3.5 bg-white border border-gray-200 rounded text-[7px] flex items-center px-1 text-gray-400">Search...</div>
                                </div>
                                <div class="p-2 space-y-1">
                                    <div class="h-2.5 bg-gray-200 rounded w-12"></div>
                                    <div class="grid grid-cols-2 gap-1">
                                        <div class="h-8 border border-gray-150 rounded bg-white"></div>
                                        <div class="h-8 border border-gray-150 rounded bg-white"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function applyPreset(sidebar, primary) {
        document.getElementById('input-sidebarColor').value = sidebar;
        document.getElementById('input-primaryColor').value = primary;
        updateColors();
    }

    function selectFont(btn, val) {
        document.querySelectorAll('.font-btn').forEach(b => {
            b.classList.remove('border-[#3b5fc0]', 'bg-blue-50', 'text-[#2a4aad]');
            b.classList.add('border-gray-200', 'text-gray-700');
        });
        btn.classList.add('border-[#3b5fc0]', 'bg-blue-50', 'text-[#2a4aad]');
        btn.classList.remove('border-gray-200', 'text-gray-700');
        document.getElementById('input-fontFamily').value = val;
        document.getElementById('preview-box').style.fontFamily = val;
    }

    function updateColors() {
        const sidebar = document.getElementById('input-sidebarColor').value;
        const primary = document.getElementById('input-primaryColor').value;
        const bg = document.getElementById('input-bgColor').value;

        document.getElementById('val-sidebarColor').textContent = sidebar.toUpperCase();
        document.getElementById('val-primaryColor').textContent = primary.toUpperCase();
        document.getElementById('val-bgColor').textContent = bg.toUpperCase();

        document.getElementById('preview-sidebar').style.backgroundColor = sidebar;
        document.getElementById('preview-content').style.backgroundColor = bg;
    }
</script>
@endsection
