@extends('user.layouts.app')

@section('title', $investor->name . ' Investments')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-3">
            <a href="{{ route('user.network.referrals') }}" class="text-gray-400 hover:text-primary transition-colors flex items-center justify-center w-8 h-8 rounded-full bg-gray-50 border border-gray-200">
                <i class="ph ph-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $investor->name }}'s Investments</h1>
        </div>
        <p class="text-gray-600 mt-1 ml-11">View all investments for this investor.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900">Investment History</h3>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-[var(--primary)]">
            Total: {{ format_currency($investments->sum('amount')) }}
        </span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead>
                <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                    <th class="px-6 py-3 font-medium">Transaction ID</th>
                    <th class="px-6 py-3 font-medium">Amount</th>
                    <th class="px-6 py-3 font-medium text-center">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($investments as $inv)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-indigo-950">{{ $inv->trx_id }}</td>
                        <td class="px-6 py-4 font-bold text-green-600">{{ format_currency($inv->amount) }}</td>
                        <td class="px-6 py-4 text-center">
                            @if(strtoupper($inv->status) === 'ACTIVE')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                    <i class="ph ph-check-circle text-green-500"></i> Active
                                </span>
                            @elseif(strtoupper($inv->status) === 'PENDING')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                    <i class="ph ph-clock text-amber-500"></i> Pending
                                </span>
                            @elseif(strtoupper($inv->status) === 'REJECTED')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                    <i class="ph ph-x-circle text-red-500"></i> Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                    {{ ucfirst($inv->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <p class="text-sm text-slate-600">{{ $inv->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $inv->created_at->format('h:i A') }}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="ph ph-folder-open text-4xl text-gray-300 mb-3"></i>
                                <p class="font-medium text-gray-600">No investments found.</p>
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
@endsection
