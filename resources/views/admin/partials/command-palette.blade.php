@php
// Build flat entries from menu for the command palette
$entries = [];
foreach (admin_sidebar_menu() as $item) {
    if (isset($item['href'])) {
        $entries[] = ['label' => $item['label'], 'group' => 'Pages', 'href' => $item['href']];
    }
    if (isset($item['children'])) {
        foreach ($item['children'] as $child) {
            $entries[] = ['label' => $child['label'], 'group' => $item['label'], 'href' => $child['href']];
        }
    }
}
@endphp

{{-- Backdrop --}}
<div id="palette-backdrop" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50" onclick="closeCommandPalette()"></div>

{{-- Modal --}}
<div id="palette-modal" class="hidden fixed inset-0 z-50 flex items-start justify-center pt-[10vh] px-4">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col" style="max-height:70vh">

        {{-- Input --}}
        <div class="flex items-center gap-3 px-4 py-3.5 border-b border-gray-100">
            @include('admin.partials.icon', ['name' => 'search', 'size' => 18])
            <input id="palette-input" type="text" placeholder="Search settings, menus, and admin pages"
                class="flex-1 text-sm text-gray-800 placeholder-gray-400 outline-none bg-transparent"
                oninput="filterPalette(this.value)"
                onkeydown="handlePaletteKey(event)">
            <button id="palette-clear" onclick="clearPalette()" class="hidden text-gray-400 hover:text-gray-600 text-xs font-medium">Clear</button>
        </div>

        {{-- Results --}}
        <div id="palette-list" class="overflow-y-auto flex-1">
            @foreach($entries as $i => $entry)
                <button
                    data-href="{{ $entry['href'] }}"
                    data-label="{{ strtolower($entry['label']) }}"
                    data-group="{{ strtolower($entry['group']) }}"
                    data-index="{{ $i }}"
                    onclick="paletteNavigate(this.dataset.href)"
                    onmouseenter="setActive({{ $i }})"
                    class="palette-item w-full flex items-center gap-3 px-4 py-3 transition-colors text-left hover:bg-gray-50 text-gray-800">
                    <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 bg-[#eef1fc] item-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[var(--theme-primary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </span>
                    <span class="flex-1 min-w-0">
                        <span class="block text-sm font-medium truncate">{{ $entry['label'] }}</span>
                        <span class="block text-xs truncate mt-0.5 text-gray-400">{{ $entry['group'] }}</span>
                    </span>
                    <svg class="item-arrow hidden w-4 h-4 text-white/70 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @endforeach
            <p id="palette-empty" class="hidden text-sm text-gray-400 text-center py-10">No results found.</p>
        </div>

        {{-- Footer --}}
        <div class="flex items-center gap-4 px-4 py-2.5 border-t border-gray-100 bg-gray-50/80">
            <span class="flex items-center gap-1 text-[11px] text-gray-400">
                <kbd class="bg-white border border-gray-200 rounded px-1 py-0.5 font-mono text-[10px] shadow-sm">↵</kbd> to select
            </span>
            <span class="flex items-center gap-1 text-[11px] text-gray-400">
                <kbd class="bg-white border border-gray-200 rounded px-1 py-0.5 font-mono text-[10px] shadow-sm">↑</kbd>
                <kbd class="bg-white border border-gray-200 rounded px-1 py-0.5 font-mono text-[10px] shadow-sm">↓</kbd> to navigate
            </span>
            <span class="flex items-center gap-1 text-[11px] text-gray-400">
                <kbd class="bg-white border border-gray-200 rounded px-1 py-0.5 font-mono text-[10px] shadow-sm">ESC</kbd> to close
            </span>
        </div>
    </div>
</div>

<script>
    let activeIndex = 0;

    function getVisibleItems() {
        return Array.from(document.querySelectorAll('.palette-item:not([style*="display: none"])'));
    }

    function openCommandPalette() {
        document.getElementById('palette-backdrop').classList.remove('hidden');
        document.getElementById('palette-modal').classList.remove('hidden');
        document.getElementById('palette-input').value = '';
        filterPalette('');
        setTimeout(() => document.getElementById('palette-input').focus(), 0);
    }

    function closeCommandPalette() {
        document.getElementById('palette-backdrop').classList.add('hidden');
        document.getElementById('palette-modal').classList.add('hidden');
    }

    function clearPalette() {
        document.getElementById('palette-input').value = '';
        document.getElementById('palette-clear').classList.add('hidden');
        filterPalette('');
        document.getElementById('palette-input').focus();
    }

    function filterPalette(query) {
        const q = query.trim().toLowerCase();
        document.getElementById('palette-clear').classList.toggle('hidden', q === '');
        let visible = 0;
        document.querySelectorAll('.palette-item').forEach(item => {
            const match = item.dataset.label.includes(q) || item.dataset.group.includes(q);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('palette-empty').classList.toggle('hidden', visible > 0);
        activeIndex = 0;
        setActive(0);
    }

    function setActive(index) {
        const items = getVisibleItems();
        items.forEach((item, i) => {
            if (i === index) {
                item.classList.add('bg-[var(--theme-primary)]', 'text-white');
                item.classList.remove('hover:bg-gray-50', 'text-gray-800');
                item.querySelector('.item-icon').classList.replace('bg-[#eef1fc]', 'bg-white/20');
                item.querySelector('.item-arrow').classList.remove('hidden');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('bg-[var(--theme-primary)]', 'text-white');
                item.classList.add('hover:bg-gray-50', 'text-gray-800');
                item.querySelector('.item-icon').classList.replace('bg-white/20', 'bg-[#eef1fc]');
                item.querySelector('.item-arrow').classList.add('hidden');
            }
        });
        activeIndex = index;
    }

    function handlePaletteKey(e) {
        const items = getVisibleItems();
        if (e.key === 'Escape') {
            closeCommandPalette();
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActive(Math.min(activeIndex + 1, items.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive(Math.max(activeIndex - 1, 0));
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const item = items[activeIndex];
            if (item) paletteNavigate(item.dataset.href);
        }
    }

    function paletteNavigate(href) {
        closeCommandPalette();
        window.location.href = href;
    }
</script>
