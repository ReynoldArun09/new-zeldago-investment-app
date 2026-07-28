@extends('admin.layouts.app')

@section('title', 'Investment Report')

@section('content')
<div class="w-full">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Investment Report</h1>
    </div>

    {{-- Top Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i class="ph ph-currency-dollar text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Invested</p>
                <h3 class="text-xl font-bold text-gray-900">{{ format_currency($totalInvested) }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="ph ph-trend-up text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Projected Returns</p>
                <h3 class="text-xl font-bold text-gray-900">{{ format_currency($projectedReturns) }}</h3>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                <i class="ph ph-arrow-up-right text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">ROI Paid Out</p>
                <h3 class="text-xl font-bold text-gray-900">{{ format_currency($roiPaidOut) }}</h3>
            </div>
        </div>
    </div>

    {{-- Middle Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Status Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-6 text-sm">Investment Status Breakdown</h3>
            <div class="space-y-5">
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-sm text-gray-600 font-medium">Active</span>
                        <span class="text-sm font-bold text-gray-800">{{ $activeCount }} <span class="text-gray-400 font-normal">({{ round(($activeCount / $totalStatusCount) * 100) }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-[var(--theme-primary)] h-2 rounded-full" style="width: {{ ($activeCount / $totalStatusCount) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-sm text-gray-600 font-medium">Completed</span>
                        <span class="text-sm font-bold text-gray-800">{{ $completedCount }} <span class="text-gray-400 font-normal">({{ round(($completedCount / $totalStatusCount) * 100) }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ ($completedCount / $totalStatusCount) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-sm text-gray-600 font-medium">Closed</span>
                        <span class="text-sm font-bold text-gray-800">{{ $closedCount }} <span class="text-gray-400 font-normal">({{ round(($closedCount / $totalStatusCount) * 100) }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-gray-400 h-2 rounded-full" style="width: {{ ($closedCount / $totalStatusCount) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Investors --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-6 text-sm">Top Investors by Volume</h3>
            <div class="space-y-4">
                @forelse($topInvestors as $index => $investor)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-indigo-50 text-[var(--theme-primary)] text-xs font-bold flex items-center justify-center shrink-0">
                                {{ $index + 1 }}
                            </div>
                            <a href="{{ $investor->username ? route('admin.users.details', $investor->username) : '#' }}" class="text-sm font-medium text-[var(--theme-primary)] hover:underline">
                                {{ $investor->username ? '@' . $investor->username : '@unknown' }}
                            </a>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ format_currency($investor->total_volume) }}</span>
                    </div>
                @empty
                    <div class="text-sm text-gray-500 text-center py-4">No investors found</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bottom Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-bold text-gray-800 mb-6 text-sm">Commission Distribution by Level</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($commissionLevels as $level)
                <div class="bg-indigo-50/50 rounded-xl p-5 text-center flex flex-col justify-center items-center">
                    <p class="text-xs font-medium text-gray-500 mb-2">Level {{ $level['level'] }}</p>
                    <h4 class="text-lg font-bold text-[var(--theme-primary)] mb-1">{{ format_currency($level['amount']) }}</h4>
                    <p class="text-[10px] text-gray-400">{{ $level['transactions'] }} transactions</p>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4 text-right">
            <p class="text-xs text-gray-500">Total commissions paid: <span class="font-bold text-emerald-600">{{ format_currency(collect($commissionLevels)->sum('amount')) }}</span></p>
        </div>
    </div>
</div>
@endsection
