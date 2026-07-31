@extends('user.layouts.app')

@section('title', $pageTitle ?? 'Transactions')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $pageTitle ?? 'Transactions' }}</h1>
        <p class="text-gray-600 mt-1">{{ $pageSubtitle ?? 'A detailed ledger of your transactions.' }}</p>
    </div>
    
    <!-- Balance Card -->
    <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 px-6 py-4 rounded-xl text-white shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
            <i class="ph ph-wallet text-2xl text-indigo-200"></i>
        </div>
        <div>
            <p class="text-indigo-200 text-sm font-medium">Available Balance</p>
            <p class="text-2xl font-black">{{ get_setting('currency_symbol', 'Rs') }}{{ number_format(auth()->user()->wallet_balance, 2) }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-none shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead>
                <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                    <th class="px-6 py-4 font-medium">Transaction ID</th>
                    <th class="px-6 py-4 font-medium">Date</th>
                    <th class="px-6 py-4 font-medium">Investment</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Amount</th>
                    <th class="px-6 py-4 font-medium text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($roiLogs as $log)
                    <tr class="hover:bg-gray-50/50 transition-colors" x-data="{ openDetails: false }">
                        <td class="px-6 py-4 font-mono text-xs text-gray-500">
                            {{ $log->trx_id }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $log->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            Investment #{{ $log->investment_id }}
                        </td>
                        <td class="px-6 py-4">
                            @if($log->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                                    <i class="ph ph-clock text-yellow-500"></i> Pending
                                </span>
                            @elseif($log->status === 'processing')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="ph ph-spinner text-blue-500 animate-spin"></i> Processing
                                </span>
                            @elseif($log->status === 'credited')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <i class="ph ph-check-circle text-green-500"></i> Confirmed
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                    <i class="ph ph-x-circle text-red-500"></i> {{ ucfirst($log->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">
                            {{ get_setting('currency_symbol', 'Rs') }}{{ number_format($log->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button @click="openDetails = true" class="px-3 py-1.5 text-xs font-medium text-[var(--theme-primary)] bg-[var(--theme-primary)]/10 hover:bg-[var(--theme-primary)]/20 rounded-md transition-colors">
                                Details
                            </button>

                            <!-- Details Modal -->
                            <div x-show="openDetails" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0" style="display: none;">
                                <div x-show="openDetails" @click="openDetails = false" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
                                <div x-show="openDetails" x-transition.scale.origin.bottom class="relative bg-white rounded-xl shadow-xl max-w-sm w-full p-6 text-left overflow-hidden">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-bold text-gray-900">ROI Details</h3>
                                        <button @click="openDetails = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <i class="ph ph-x text-lg"></i>
                                        </button>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Status</p>
                                            <p class="text-sm font-medium text-gray-900">{{ ucfirst($log->status) }}</p>
                                        </div>
                                        @if($log->payment_method)
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Payment Method</p>
                                            <p class="text-sm font-medium text-gray-900">{{ $log->payment_method }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 mb-1">Transaction ID</p>
                                            <p class="text-sm font-medium text-gray-900 break-all">{{ $log->payment_trx_id }}</p>
                                        </div>
                                        @endif
                                        @if($log->payment_proof)
                                        <div>
                                            <p class="text-xs text-gray-500 mb-2">Payment Proof</p>
                                            <a href="{{ asset('storage/' . $log->payment_proof) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:border-[var(--theme-primary)] transition-colors">
                                                <img src="{{ asset('storage/' . $log->payment_proof) }}" alt="Payment Proof" class="w-full h-auto object-cover max-h-48">
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="ph ph-receipt text-4xl text-gray-300 mb-3"></i>
                                <p class="font-medium text-gray-600">No transactions found.</p>
                                <p class="text-sm mt-1">Your ledger is currently empty.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($roiLogs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $roiLogs->links() }}
        </div>
    @endif
</div>
@endsection
