@extends('admin.layouts.app')

@section('title', 'All Withdrawals')

@section('content')
<div class="min-h-full p-4 sm:p-6 space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-none shadow-sm flex flex-col justify-center">
            <p class="text-xs text-gray-500 font-medium">Total Withdrawn</p>
            <p class="text-xl font-bold text-gray-800 mt-1">{{ format_currency($totalWithdrawals) }}</p>
        </div>
        <div class="bg-white p-4 rounded-none shadow-sm flex flex-col justify-center">
            <p class="text-xs text-gray-500 font-medium">Approved Requests</p>
            <p class="text-xl font-bold text-green-600 mt-1">{{ $approvedCount }}</p>
        </div>
        <div class="bg-white p-4 rounded-none shadow-sm flex flex-col justify-center">
            <p class="text-xs text-gray-500 font-medium">Pending Requests</p>
            <p class="text-xl font-bold text-amber-500 mt-1">{{ $pendingCount }}</p>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-700">All Withdrawals</h1>
    </div>

    <div class="bg-white shadow-sm flex flex-col rounded-none">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-white" style="background-color: var(--theme-primary);">
                        <th class="text-left px-5 py-3 font-medium">User</th>
                        <th class="text-left px-5 py-3 font-medium">Method</th>
                        <th class="text-right px-5 py-3 font-medium">Amount</th>
                        <th class="text-left px-5 py-3 font-medium">Trx ID</th>
                        <th class="text-center px-5 py-3 font-medium">Status</th>
                        <th class="text-right px-5 py-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($withdrawals as $withdrawal)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ $withdrawal->user->name ?? 'N/A' }}</p>
                            <p class="text-xs" style="color: var(--theme-primary);">{{ '@' . ($withdrawal->user->username ?? '') }}</p>
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-600">
                            {{ $withdrawal->payout_method }}
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-gray-800">
                            {{ format_currency($withdrawal->amount) }}
                        </td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-500">
                            {{ $withdrawal->trx_id ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($withdrawal->status === 'pending')
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold uppercase">Pending</span>
                            @elseif($withdrawal->status === 'approved')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold uppercase">Approved</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-bold uppercase">Rejected</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right text-xs text-gray-500">
                            {{ $withdrawal->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-xs text-gray-400">No withdrawals found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($withdrawals->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Showing {{ $withdrawals->firstItem() ?? 0 }} to {{ $withdrawals->lastItem() ?? 0 }} of {{ $withdrawals->total() }} results
            </span>
            <div>
                {{ $withdrawals->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
        @else
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Showing {{ $withdrawals->count() }} to {{ $withdrawals->count() }} of {{ $withdrawals->count() }} results
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
