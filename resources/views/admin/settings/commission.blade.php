@extends('admin.layouts.app')

@section('title', 'Commission Settings')

@section('content')
<div class="w-full" x-data="commissionSettings({{ $settings->level_count ?? 4 }}, {{ json_encode($settings->commissions ?? [1 => 10.5, 2 => 5.0, 3 => 2.5, 4 => 1.0]) }})">
    {{-- Header --}}
    <form method="POST" action="{{ route('admin.settings.commission.update') }}">
        @csrf
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-lg font-bold text-gray-800">Commission Settings</h1>
                <p class="text-xs text-gray-500 mt-0.5">Configure dynamic referral and affiliate commission structures.</p>
            </div>
            <button type="submit" class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-[var(--theme-primary)] hover:opacity-90 rounded-lg transition-all shadow-sm font-semibold">
                <i class="ph ph-floppy-disk text-lg"></i>
                Save Changes
            </button>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm font-medium">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow-sm mb-6">
            <div class="mb-6 pb-6 border-b border-gray-100">
                <label for="level_count" class="block text-sm font-semibold text-gray-700 mb-2">Number of Levels</label>
                <div class="flex items-center gap-4 max-w-sm">
                    <input type="number" id="level_count" name="level_count" x-model.number="levelCount" @input="updateLevels" class="block w-full px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-[var(--theme-primary)] focus:border-[var(--theme-primary)] transition-colors" required min="1" max="50">
                    <span class="text-sm text-gray-500 whitespace-nowrap">Enter how many tiers you want to reward.</span>
                </div>
            </div>
            
            <h3 class="text-md font-semibold text-gray-800 mb-4">Commission Percentages</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <template x-for="level in levelCount" :key="level">
                    <div>
                        <label :for="'commission_' + level" class="block text-sm font-semibold text-gray-700 mb-2" x-text="'Level ' + level + ' Commission (%)'"></label>
                        <div class="relative">
                            <input type="number" step="0.01" :id="'commission_' + level" :name="'commissions[' + level + ']'" x-model="commissions[level]" class="block w-full pl-4 pr-10 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-[var(--theme-primary)] focus:border-[var(--theme-primary)] transition-colors" required min="0" max="100">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                %
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('commissionSettings', (initialCount, initialCommissions) => ({
            levelCount: initialCount,
            commissions: initialCommissions,
            
            updateLevels() {
                if (this.levelCount < 1) this.levelCount = 1;
                if (this.levelCount > 50) this.levelCount = 50;
                
                // Initialize missing levels with 0
                for (let i = 1; i <= this.levelCount; i++) {
                    if (this.commissions[i] === undefined) {
                        this.commissions[i] = 0;
                    }
                }
            }
        }))
    })
</script>
@endsection
