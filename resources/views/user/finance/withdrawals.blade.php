@extends('user.layouts.app')

@section('title', 'Withdrawals')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Withdrawals</h1>
        <p class="text-gray-600 mt-1">Request a payout of your available balance.</p>
    </div>
    
    <!-- Balance Card -->
    <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden min-w-[240px]" style="background-color: #f39c12;">
        <div class="p-4 flex flex-col justify-center">
            <p class="text-xs mb-1 font-medium opacity-90">Available Balance</p>
            <p class="text-xl font-bold tracking-wide">{{ get_setting('currency_symbol', 'Rs') }}{{ number_format($available_balance, 2) }}</p>
        </div>
        <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
            <i class="ph ph-wallet text-2xl opacity-90"></i>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3">
        <i class="ph ph-check-circle text-green-600 text-xl shrink-0"></i>
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="ph ph-x-circle text-red-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Request Form -->
    <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Request Payout</h3>
        
        <form action="{{ route('user.finance.withdrawals.submit') }}" method="POST" class="space-y-4" onsubmit="return confirm('Are you sure you want to submit this withdrawal request?');">
            @csrf
            
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount ({{ get_setting('currency_symbol', 'Rs') }})</label>
                <input type="number" name="amount" id="amount" min="10" max="{{ $available_balance }}" step="0.01" required
                    class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50" placeholder="0.00">
                <p class="text-xs text-gray-500 mt-1">Minimum withdrawal is {{ get_setting('currency_symbol', 'Rs') }}10.00.</p>
            </div>
            

            
            <button type="submit" class="w-full px-4 py-3 bg-primary text-white rounded-xl font-medium shadow-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2 mt-2">
                <i class="ph ph-paper-plane-tilt"></i>
                Submit Request
            </button>
        </form>
    </div>
    
    <!-- History Table -->
    <div class="lg:col-span-2 bg-white rounded-sm shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Withdrawal History</h3>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Amount</th>

                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($withdrawals as $withdrawal)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">
                                {{ $withdrawal->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ get_setting('currency_symbol', 'Rs') }}{{ number_format($withdrawal->amount, 2) }}
                            </td>

                            <td class="px-6 py-4">
                                @if($withdrawal->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ph ph-clock text-amber-500"></i> Pending
                                    </span>
                                @elseif($withdrawal->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                        <i class="ph ph-check-circle text-green-500"></i> Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200" title="{{ $withdrawal->admin_message }}">
                                        <i class="ph ph-x-circle text-red-500"></i> Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right" x-data="{ openDetails: false }">
                                @if($withdrawal->status === 'approved')
                                    <button @click="openDetails = true" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 underline">Details</button>
                                    
                                    <!-- Details Modal -->
                                    <div x-show="openDetails" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="openDetails" @click="openDetails = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                                            </div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div x-show="openDetails" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                                                <div class="bg-white px-6 pt-5 pb-4">
                                                    <h3 class="text-lg font-bold text-gray-800 mb-4 text-left">
                                                        Withdrawal Approved
                                                    </h3>
                                                    <hr class="border-gray-100 mb-4 -mx-6">
                                                    
                                                    <div class="space-y-4 text-left">
                                                        <div>
                                                            <p class="text-sm text-gray-500 mb-1">Transaction ID / Hash</p>
                                                            <p class="font-mono text-gray-800 bg-gray-50 p-2 rounded-lg border border-gray-200 break-all">{{ $withdrawal->trx_id }}</p>
                                                        </div>
                                                        @if($withdrawal->proof_image)
                                                            <div class="mt-4">
                                                                <p class="text-xs font-medium text-gray-500 mb-2">Payment Proof</p>
                                                            <img src="{{ Storage::url($withdrawal->proof_image) }}" alt="Proof" class="w-full rounded-lg border border-gray-200 shadow-sm max-h-64 object-contain">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="bg-white px-6 py-4 border-t border-gray-100 flex justify-end">
                                                    <button type="button" @click="openDetails = false" class="rounded-lg px-4 py-2 bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($withdrawal->status === 'rejected')
                                    <button @click="alert('{{ addslashes($withdrawal->admin_message) }}')" class="text-xs font-medium text-red-600 hover:text-red-800 underline">Reason</button>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="ph ph-wallet text-4xl text-gray-300 mb-3"></i>
                                    <p class="font-medium text-gray-600">No withdrawals yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $withdrawals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
