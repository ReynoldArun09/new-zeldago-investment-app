@extends('admin.layouts.app')

@section('title', 'Commissions Report')

@section('content')
<div class="w-full">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Commissions Report</h1>
    </div>

    {{-- Top Cards (Dynamic based on levels) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach($commissionLevels as $level)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col justify-center">
                <p class="text-[11px] text-gray-500 font-medium mb-1">Level {{ $level['level'] }} Total</p>
                <h3 class="text-lg font-bold text-[var(--theme-primary)]">{{ format_currency($level['amount']) }}</h3>
            </div>
        @endforeach
    </div>

    {{-- Middle Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Distribution by Level --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-6 text-sm">Distribution by Level</h3>
            <div class="space-y-5">
                @foreach($commissionLevels as $level)
                    @php
                        $percentage = $totalCommission > 0 ? round(($level['amount'] / $totalCommission) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-xs text-gray-600 font-medium">Level {{ $level['level'] }}</span>
                            <span class="text-xs font-bold text-gray-800">{{ format_currency($level['amount']) }} <span class="text-gray-400 font-normal">({{ $percentage }}%)</span></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-gray-200 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6 text-right border-t border-gray-50 pt-4">
                <span class="text-xs text-gray-500 mr-2">Total:</span>
                <span class="text-sm font-bold text-emerald-600">{{ format_currency($totalCommission) }}</span>
            </div>
        </div>

        {{-- Top Earning Agents --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-6 text-sm">Top Earning Users</h3>
            <div class="space-y-4">
                @forelse($topEarners as $index => $earner)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold flex items-center justify-center shrink-0">
                                {{ $index + 1 }}
                            </div>
                            <a href="{{ $earner->username ? route('admin.users.details', $earner->username) : '#' }}" class="text-sm font-medium text-[var(--theme-primary)] hover:underline">
                                {{ $earner->username ? '@' . $earner->username : '@unknown' }}
                            </a>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ format_currency($earner->total_earned) }}</span>
                    </div>
                @empty
                    <div class="text-sm text-gray-500 py-4">No data available</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
