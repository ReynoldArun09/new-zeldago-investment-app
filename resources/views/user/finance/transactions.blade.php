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

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50/50 text-gray-500 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Transaction ID</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Description</th>
                    <th class="px-6 py-4 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs text-gray-500">
                            TXN-{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $transaction->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($transaction->type === 'commission')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <i class="ph ph-trend-up text-green-500"></i> Commission
                                </span>
                            @elseif($transaction->type === 'withdrawal')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                    <i class="ph ph-money text-red-500"></i> Withdrawal
                                </span>
                            @elseif($transaction->type === 'ROI')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="ph ph-chart-line-up text-blue-500"></i> ROI Return
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <i class="ph ph-briefcase text-primary"></i> Investment
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700 font-medium">
                            {{ $transaction->description }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold {{ $transaction->amount > 0 ? 'text-green-600' : 'text-gray-900' }}">
                            {{ $transaction->amount > 0 ? '+' : '' }}{{ get_setting('currency_symbol', 'Rs') }}{{ number_format($transaction->amount, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
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
    
    @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
@endsection
