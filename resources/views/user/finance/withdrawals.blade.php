@extends('user.layouts.app')

@section('title', 'Withdrawals')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Withdrawals</h1>
        <p class="text-gray-600 mt-1">Request a payout of your available balance.</p>
    </div>
    
    <!-- Balance Card -->
    <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 px-6 py-4 rounded-xl text-white shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
            <i class="ph ph-wallet text-2xl text-indigo-200"></i>
        </div>
        <div>
            <p class="text-indigo-200 text-sm font-medium">Available Balance</p>
            <p class="text-2xl font-black">${{ number_format(auth()->user()->balance, 2) }}</p>
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
        
        <form action="{{ route('user.finance.withdrawals.submit') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                <input type="number" name="amount" id="amount" min="10" max="{{ auth()->user()->balance }}" step="0.01" required
                    class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50" placeholder="0.00">
                <p class="text-xs text-gray-500 mt-1">Minimum withdrawal is $10.00.</p>
            </div>
            
            <div>
                <label for="payout_method" class="block text-sm font-medium text-gray-700 mb-1">Payout Method</label>
                <select name="payout_method" id="payout_method" required class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50">
                    <option value="">Select Method...</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Crypto (USDT)">Crypto (USDT TRC20)</option>
                    <option value="PayPal">PayPal</option>
                </select>
            </div>
            
            <div>
                <label for="payout_details" class="block text-sm font-medium text-gray-700 mb-1">Payout Details</label>
                <textarea name="payout_details" id="payout_details" rows="3" required placeholder="Enter bank account details, wallet address, or PayPal email..."
                    class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50"></textarea>
            </div>
            
            <button type="submit" class="w-full px-4 py-3 bg-primary text-white rounded-xl font-medium shadow-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2 mt-2">
                <i class="ph ph-paper-plane-tilt"></i>
                Submit Request
            </button>
        </form>
    </div>
    
    <!-- History Table -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Withdrawal History</h3>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-gray-50/50 text-gray-500 font-medium border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Method</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($withdrawals as $withdrawal)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">
                                {{ $withdrawal->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">
                                ${{ number_format($withdrawal->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $withdrawal->payout_method }}
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
