@extends('admin.layouts.app')

@section('title', 'Commission Log')

@section('content')
<div class="w-full">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-800">Commission Log</h1>
        <p class="text-sm text-gray-500 mt-1">Total paid: <span class="font-bold text-emerald-600">{{ format_currency($totalPaid) }}</span></p>
    </div>

    {{-- Top Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach($levelTotals as $level => $total)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <p class="text-[11px] text-gray-500 font-medium mb-1">Level {{ $level }} Commissions</p>
                <h3 class="text-lg font-bold text-[var(--theme-primary)]">{{ format_currency($total) }}</h3>
            </div>
        @endforeach
    </div>

    {{-- Search and Table --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex justify-end">
            <form action="{{ route('admin.commission-log') }}" method="GET" class="flex items-center gap-2 max-w-sm w-full">
                <select name="level" class="text-sm border-gray-200 rounded-lg focus:ring-[var(--theme-primary)] focus:border-[var(--theme-primary)]">
                    <option value="">All Levels</option>
                    @foreach($levelTotals as $level => $total)
                        <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>Level {{ $level }}</option>
                    @endforeach
                </select>
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="To User / From User" class="w-full text-sm border-gray-200 rounded-lg focus:ring-[var(--theme-primary)] focus:border-[var(--theme-primary)] pl-4 pr-10 py-2">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="ph ph-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-[var(--theme-primary)] text-white">
                    <tr>
                        <th class="px-5 py-3 font-medium text-xs tracking-wider">Trx ID</th>
                        <th class="px-5 py-3 font-medium text-xs tracking-wider">To User</th>
                        <th class="px-5 py-3 font-medium text-xs tracking-wider">From User</th>
                        <th class="px-5 py-3 font-medium text-xs tracking-wider">Investment Trx</th>
                        <th class="px-5 py-3 font-medium text-xs tracking-wider">Level</th>
                        <th class="px-5 py-3 font-medium text-xs tracking-wider">Rate</th>
                        <th class="px-5 py-3 font-medium text-xs tracking-wider">Amount</th>
                        <th class="px-5 py-3 font-medium text-xs tracking-wider">Credited At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($commissions as $commission)
                        @php
                            $levelNum = 'N/A';
                            if (preg_match('/Level (\d+)/i', $commission->description, $matches)) {
                                $levelNum = $matches[1];
                            }
                            $roi = $commission->reference_id ? ($roiLogs[$commission->reference_id] ?? null) : null;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3 font-medium text-gray-800">TRX-C{{ str_pad($commission->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.users.details', $commission->user->username) }}" class="text-[var(--theme-primary)] hover:underline font-medium">
                                    {{ '@' . $commission->user->username }}
                                </a>
                            </td>
                            <td class="px-5 py-3">
                                @if($roi && $roi->user)
                                    <a href="{{ route('admin.users.details', $roi->user->username) }}" class="text-gray-700 hover:underline">
                                        {{ '@' . $roi->user->username }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                @if($roi && $roi->investment)
                                    <a href="{{ route('admin.investments.show', $roi->investment->id ?? 0) }}" class="hover:underline">
                                        {{ $roi->investment->trx_id ?? 'N/A' }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                    L{{ $levelNum }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                @if($roi && $roi->rate > 0)
                                    {{ $roi->rate }}%
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-bold text-emerald-600">
                                {{ format_currency($commission->amount) }}
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-xs">
                                {{ $commission->created_at->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-gray-400">
                                Data not found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($commissions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Showing {{ $commissions->firstItem() ?? 0 }} to {{ $commissions->lastItem() ?? 0 }} of {{ $commissions->total() }} results
            </span>
            <div>
                {{ $commissions->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
        @else
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Showing {{ $commissions->count() }} to {{ $commissions->count() }} of {{ $commissions->count() }} results
            </span>
            <div class="flex gap-1">
                <button disabled class="px-3 py-1 border border-gray-100 rounded-md bg-gray-50 text-gray-400 text-sm">&lt;</button>
                <button class="px-3 py-1 border border-[var(--theme-primary)] rounded-md bg-[var(--theme-primary)] text-white text-sm">1</button>
                <button disabled class="px-3 py-1 border border-gray-100 rounded-md bg-gray-50 text-gray-400 text-sm">&gt;</button>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
