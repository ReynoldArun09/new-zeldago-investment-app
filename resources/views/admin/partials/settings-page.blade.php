{{--
    Reusable settings page component.
    Props:
      $title  (string)  - Page heading
      $cards  (array)   - [['icon' => '', 'title' => '', 'description' => '', 'href' => ''], ...]
--}}
<div>
    <h1 class="text-xl font-bold text-gray-800 mb-5">{{ $title }}</h1>

    {{-- Search --}}
    <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-4 py-2.5 mb-6 bg-white shadow-sm">
        @include('admin.partials.icon', ['name' => 'search', 'size' => 16])
        <input
            id="settings-search"
            type="text"
            placeholder="Search..."
            oninput="filterSettings(this.value)"
            class="flex-1 text-sm text-gray-700 placeholder-gray-400 outline-none bg-transparent"
        >
    </div>

    {{-- Empty state --}}
    <p id="settings-empty" class="hidden text-sm text-gray-400 text-center py-16">No settings found.</p>

    {{-- Cards grid --}}
    <div id="settings-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($cards as $card)
            <a href="{{ $card['href'] }}"
                data-label="{{ strtolower($card['title']) }} {{ strtolower($card['description']) }}"
                class="settings-card group flex items-center gap-4 bg-white border border-gray-100 rounded-xl p-5 text-left
                       hover:border-[#3b5fc0]/30 hover:shadow-md hover:shadow-[#3b5fc0]/5 transition-all duration-200">
                {{-- Icon --}}
                <div class="w-12 h-12 rounded-lg bg-[var(--theme-primary)] flex items-center justify-center shrink-0
                            group-hover:scale-105 transition-transform duration-200 text-white">
                    @include('admin.partials.icon', ['name' => $card['icon'], 'size' => 20])
                </div>
                {{-- Text --}}
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 group-hover:text-[var(--theme-primary)] transition-colors truncate">
                        {{ $card['title'] }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed line-clamp-2">
                        {{ $card['description'] }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</div>

<script>
    function filterSettings(query) {
        const q = query.toLowerCase();
        let visible = 0;
        document.querySelectorAll('.settings-card').forEach(card => {
            const match = card.dataset.label.includes(q);
            card.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('settings-empty').classList.toggle('hidden', visible > 0);
    }
</script>
