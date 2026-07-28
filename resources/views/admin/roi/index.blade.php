@extends('admin.layouts.app')

@section('title', 'ROI Payment Log')

@section('content')
<div class="w-full">
    {{-- Top Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Total ROI Paid</h3>
            <p class="text-2xl font-bold text-emerald-600">{{ format_currency($totalRoiPaid) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Total Records</h3>
            <p class="text-2xl font-bold text-[var(--theme-primary)]">{{ number_format($totalRecords) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Credited</h3>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($creditedCount) }}</p>
        </div>
        <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pending</h3>
            <p class="text-2xl font-bold text-amber-500">{{ number_format($pendingCount) }}</p>
        </div>
    </div>

    {{-- Main Box --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-lg font-bold text-gray-800">ROI Payment Log</h2>
            <form action="{{ route('admin.roi.index') }}" method="GET" class="flex">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Username / TrxID" class="w-full md:w-64 px-4 py-2 text-sm border border-gray-200 rounded-l-lg focus:outline-none focus:border-[var(--theme-primary)]">
                <button type="submit" class="px-4 py-2 bg-[var(--theme-primary)] text-white rounded-r-lg hover:opacity-90 transition-opacity">
                    <i class="ph ph-magnifying-glass"></i>
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-white">
                <thead class="bg-[var(--theme-primary)] font-medium">
                    <tr>
                        <th class="px-6 py-3.5">Trx ID</th>
                        <th class="px-6 py-3.5">Investment Trx</th>
                        <th class="px-6 py-3.5">User</th>
                        <th class="px-6 py-3.5">ROI Amount</th>
                        <th class="px-6 py-3.5">Rate</th>
                        <th class="px-6 py-3.5">Credited At</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs">{{ $log->trx_id }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $log->investment->trx_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($log->user->username)
                                <a href="{{ route('admin.users.details', $log->user->username) }}" class="font-medium text-[var(--theme-primary)] hover:underline">
                                    {{ $log->user->username }}
                                </a>
                                @else
                                <span class="font-medium text-gray-500">No Username</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold text-emerald-600">
                                {{ format_currency($log->amount) }}
                            </td>
                            <td class="px-6 py-4">{{ number_format($log->rate, 2) }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $log->updated_at->format('Y-m-d H:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($log->status === 'credited')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        Credited
                                    </span>
                                @elseif($log->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200/60">
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($log->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" onclick="openApproveModal({{ $log->id }}, '{{ $log->trx_id }}')" class="px-3 py-1.5 text-xs font-medium text-white bg-emerald-500 rounded-lg hover:bg-emerald-600 transition-colors">
                                            Approve
                                        </button>
                                        <button type="button" onclick="openRejectModal({{ $log->id }}, '{{ $log->trx_id }}')" class="px-3 py-1.5 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors">
                                            Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                Data not found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} results
                </span>
                <div>
                    {{ $logs->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        @else
            <div class="px-6 py-4 border-t border-gray-100">
                <span class="text-sm text-gray-500">
                    Showing {{ $logs->count() }} to {{ $logs->count() }} of {{ $logs->count() }} results
                </span>
            </div>
        @endif
    </div>
</div>

{{-- Approve Modal --}}
<div id="approve-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800" id="approve-modal-title">Approve ROI</h3>
            <button onclick="document.getElementById('approve-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form id="approve-form" method="POST" action="" class="p-5 space-y-5">
            @csrf
            
            @php
                $roi = \App\Models\Setting::where('key', 'roi_settings')->first();
                $settings = $roi ? $roi->value : [];
                $roiTypes = $settings['roi_type'] ?? ['manual'];
            @endphp
            
            @if(in_array('manual', $roiTypes))
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (Fixed)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">$</span>
                    <input type="number" name="amount" step="0.01" class="w-full border border-gray-200 rounded-lg pl-8 pr-4 py-2.5 text-sm focus:border-[var(--theme-primary)] focus:ring-1 focus:ring-[var(--theme-primary)] outline-none" placeholder="0.00">
                </div>
                <p class="text-xs text-gray-500 mt-1">Enter a fixed amount to credit to the user.</p>
            </div>
            @endif
            
            @if(in_array('auto', $roiTypes))
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Percentage (Auto)</label>
                <div class="relative">
                    <input type="number" name="rate" step="0.01" class="w-full border border-gray-200 rounded-lg pl-4 pr-8 py-2.5 text-sm focus:border-[var(--theme-primary)] focus:ring-1 focus:ring-[var(--theme-primary)] outline-none" placeholder="0.00">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 font-medium">%</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Calculated based on the original investment amount.</p>
            </div>
            @endif

            <div class="pt-2 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('approve-modal').classList.add('hidden')" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[var(--theme-primary)] rounded-lg hover:opacity-90 transition-opacity shadow-sm">Confirm Approval</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openApproveModal(id, trx) {
        document.getElementById('approve-modal').classList.remove('hidden');
        document.getElementById('approve-modal-title').innerText = 'Approve ROI - ' + trx;
        document.getElementById('approve-form').action = '/admin/roi/' + id + '/approve';
    }
</script>

{{-- Reject Modal --}}
<div id="reject-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800" id="reject-modal-title">Reject ROI</h3>
            <button onclick="document.getElementById('reject-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form id="reject-form" method="POST" action="" class="p-5 space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rejection Note</label>
                <textarea name="reject_note" required rows="3" class="w-full border border-gray-200 rounded-lg p-3 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none" placeholder="Explain why this request is being rejected..."></textarea>
                <p class="text-xs text-gray-500 mt-1">This note will be sent to the user as a notification.</p>
            </div>

            <div class="pt-2 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:opacity-90 transition-opacity shadow-sm">Reject Request</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id, trx) {
        document.getElementById('reject-modal').classList.remove('hidden');
        document.getElementById('reject-modal-title').innerText = 'Reject ROI - ' + trx;
        document.getElementById('reject-form').action = '/admin/roi/' + id + '/reject';
    }
</script>
@endsection
