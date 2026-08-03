@extends('user.layouts.app')

@section('title', 'Closed Investments')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Closed Investments</h1>
        <p class="text-gray-600 mt-1">View your completed and rejected investment requests.</p>
    </div>

    <div class="bg-white rounded-none shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                        <th class="px-6 py-4 font-medium">Transaction ID</th>
                        <th class="px-6 py-4 font-medium">Amount</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium text-center">Proof</th>
                        <th class="px-6 py-4 font-medium text-center">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($investments as $inv)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $inv->trx_id }}</td>
                            <td class="px-6 py-4 font-medium">{{ format_currency($inv->amount) }}</td>
                            <td class="px-6 py-4">
                                @if($inv->status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                        <i class="ph ph-x-circle text-red-500"></i> Rejected
                                    </span>
                                @elseif($inv->status === 'completed' || $inv->status === 'closed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="ph ph-check-circle text-indigo-500"></i> Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                        {{ ucfirst($inv->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $inv->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($inv->payment_proof)
                                    <a href="{{ Storage::url($inv->payment_proof) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:text-primary hover:bg-indigo-50 transition-colors">
                                        <i class="ph ph-image text-lg"></i>
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div x-data="{ openDetails: false }">
                                    <button type="button" @click="openDetails = true" class="inline-flex items-center justify-center w-8 h-8 rounded-none bg-gray-50 text-gray-500 hover:text-[var(--primary)] hover:bg-indigo-50 transition-colors">
                                        <i class="ph ph-eye text-lg"></i>
                                    </button>

                                    <!-- Details Modal -->
                                    <div x-show="openDetails" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="openDetails" @click="openDetails = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true"></div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div x-show="openDetails" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-none text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                                                <div class="bg-white px-6 pt-5 pb-5 text-left">
                                                    <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                                                        <h3 class="text-lg font-bold text-gray-900">Investment Details</h3>
                                                        <button type="button" @click="openDetails = false" class="text-gray-400 hover:text-gray-500 transition-colors">
                                                            <i class="ph ph-x text-lg"></i>
                                                        </button>
                                                    </div>
                                                    <div class="space-y-3 text-sm">
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-gray-500">Transaction ID</span>
                                                            <span class="font-medium text-gray-900">{{ $inv->trx_id }}</span>
                                                        </div>
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-gray-500">Amount</span>
                                                            <span class="font-bold text-gray-900">{{ format_currency($inv->amount) }}</span>
                                                        </div>
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-gray-500">Status</span>
                                                            <span class="font-medium">
                                                                @if($inv->status === 'rejected')
                                                                    <span class="text-red-600 font-bold uppercase text-xs">Rejected</span>
                                                                @elseif($inv->status === 'completed' || $inv->status === 'closed')
                                                                    <span class="text-indigo-600 font-bold uppercase text-xs">Completed</span>
                                                                @else
                                                                    <span class="text-gray-600 font-bold uppercase text-xs">{{ $inv->status }}</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-gray-500">Date</span>
                                                            <span class="font-medium text-gray-900">{{ $inv->created_at->format('M d, Y h:i A') }}</span>
                                                        </div>
                                                        
                                                        @if($inv->admin_message)
                                                            <div class="mt-5 pt-4 border-t border-gray-100">
                                                                <span class="block text-gray-500 mb-2 font-bold text-xs uppercase tracking-wider">Admin Note:</span>
                                                                <div class="bg-amber-50 p-3 rounded-none text-amber-900 text-sm border border-amber-200">
                                                                    {{ $inv->admin_message }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="ph ph-folder text-4xl text-gray-300 mb-3"></i>
                                    <p class="font-medium text-gray-600">No closed investments found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($investments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $investments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
