@extends('admin.layouts.app')

@section('title', 'Pending ROI Requests')

@section('content')
<div class="w-full">
    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- Main Box --}}
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-lg font-bold text-gray-800">Processing ROI Requests</h2>
            <form action="{{ route('admin.roi.processing') }}" method="GET" class="flex">
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
                        <th class="px-6 py-3.5 text-center">ROI Amount</th>
                        <th class="px-6 py-3.5 text-center">Rate</th>
                        <th class="px-6 py-3.5 text-center">Generated At</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-xs text-[var(--theme-primary)] uppercase">{{ $log->trx_id }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500">{{ $log->investment->trx_id ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 text-sm">{{ $log->user->name ?? 'User' }}</div>
                                @if($log->user->username)
                                <a href="{{ route('admin.users.details', $log->user->username) }}" class="text-xs text-[var(--theme-primary)] hover:underline">
                                    {{ '@' . $log->user->username }}
                                </a>
                                @else
                                <span class="text-xs text-gray-400">No Username</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-600 text-center">
                                {{ format_currency($log->amount) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-600">
                                    {{ number_format($log->rate, 0) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 text-center">
                                {{ $log->created_at->format('m/d/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-blue-500 border border-blue-400 uppercase">
                                    Processing
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2" x-data="{ openApprove: false, openReject: false }">
                                    <button type="button" @click="openApprove = true" class="px-3 py-1 text-white bg-[#00A843] hover:bg-green-700 rounded text-[11px] font-medium transition-colors">
                                        Approve
                                    </button>
                                    <button type="button" @click="openReject = true" class="px-3 py-1 text-white bg-[#E2000F] hover:bg-red-700 rounded text-[11px] font-medium transition-colors">
                                        Reject
                                    </button>

                                    <!-- Approve Modal -->
                                    <div x-show="openApprove" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="openApprove" @click="openApprove = false" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                                            </div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div x-show="openApprove" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full" x-data="{ step: 1 }">
                                                <form action="{{ route('admin.roi.approve', $log->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="bg-white px-6 pt-5 pb-4">
                                                        <h3 class="text-xl font-bold text-gray-800 mb-4 text-left" id="modal-title">
                                                            Approve ROI Request
                                                        </h3>
                                                        <hr class="border-gray-100 mb-4 -mx-6">
                                                        
                                                        <!-- Step 1: Amount / Percentage -->
                                                        <div x-show="step === 1" class="space-y-4">
                                                            @if(in_array('manual', $roiSettings['roi_type'] ?? []))
                                                            <div>
                                                                <label class="block text-sm text-gray-600 mb-1">ROI Payout Amount ({{ default_currency() }})</label>
                                                                <input type="number" step="any" name="amount" value="{{ $log->amount }}" class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] focus:ring-0 text-sm p-3" placeholder="e.g. 1500">
                                                            </div>
                                                            @endif
                                                            
                                                            @if(in_array('auto', $roiSettings['roi_type'] ?? []))
                                                            <div>
                                                                <label class="block text-sm text-gray-600 mb-1">ROI Percentage (%)</label>
                                                                <input type="number" step="any" name="rate" value="{{ $log->rate }}" class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] focus:ring-0 text-sm p-3" placeholder="e.g. 5">
                                                            </div>
                                                            @endif

                                                            @if($log->investment && $log->investment->is_old)
                                                            <div>
                                                                <label class="block text-sm text-gray-600 mb-1">Direct ROI Amount</label>
                                                                <input type="number" step="any" name="direct_roi_amount" class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] focus:ring-0 text-sm p-3" placeholder="e.g. 500">
                                                                <p class="text-[10px] text-[var(--theme-primary)] font-medium mt-1">This field is only applicable for old investments.</p>
                                                            </div>
                                                            @endif
                            
                                                            <p class="text-xs text-gray-500">This amount will be credited to the user's account upon approval.</p>
                                                        </div>

                                                        <!-- Step 2: Payment Details -->
                                                        <div x-show="step === 2" class="space-y-4" style="display: none;">
                                                            <div>
                                                                <label class="block text-sm text-gray-600 mb-1">Payment Method</label>
                                                                <select name="payment_method" required class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] focus:ring-0 text-sm p-3">
                                                                    <option value="">Select Method</option>
                                                                    <option value="UPI">UPI</option>
                                                                    <option value="Bank Transfer">Bank Transfer</option>
                                                                    <option value="Crypto">Crypto</option>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm text-gray-600 mb-1">Transaction ID</label>
                                                                <input type="text" name="payment_trx_id" required class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] focus:ring-0 text-sm p-3" placeholder="Enter Trx ID">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm text-gray-600 mb-1">Payment Proof Image</label>
                                                                <input type="file" name="payment_proof" accept="image/*" required class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] focus:ring-0 text-sm p-2">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="bg-white px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                                                        
                                                        <button type="button" x-show="step === 1" @click="step = 2" class="inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-[var(--theme-primary)] text-sm font-medium text-white hover:opacity-90 focus:outline-none transition-opacity">
                                                            Next
                                                        </button>

                                                        <button type="submit" x-show="step === 2" class="inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-[var(--theme-primary)] text-sm font-medium text-white hover:opacity-90 focus:outline-none transition-opacity" style="display: none;">
                                                            Approve & Credit
                                                        </button>

                                                        <button type="button" x-show="step === 2" @click="step = 1" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors" style="display: none;">
                                                            Back
                                                        </button>

                                                        <button type="button" x-show="step === 1" @click="openApprove = false" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div x-show="openReject" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="openReject" @click="openReject = false" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                                            </div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div x-show="openReject" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                                                <form action="{{ route('admin.roi.reject', $log->id) }}" method="POST">
                                                    @csrf
                                                    <div class="bg-white px-6 pt-5 pb-4">
                                                        <h3 class="text-xl font-bold text-gray-800 mb-4" id="modal-title-reject">
                                                            Reject ROI Request
                                                        </h3>
                                                        <hr class="border-gray-100 mb-4 -mx-6">
                                                        <div class="space-y-4">
                                                            <div>
                                                                <label class="block text-sm text-gray-600 mb-1">Rejection Note</label>
                                                                <textarea name="reject_note" required rows="3" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm p-3 outline-none" placeholder="Explain why this request is being rejected..."></textarea>
                                                            </div>
                                                            <p class="text-xs text-gray-500">This note will be sent to the user as a notification.</p>
                                                        </div>
                                                    </div>
                                                    <div class="bg-white px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                                                        <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-[#E2000F] text-sm font-medium text-white hover:bg-red-700 focus:outline-none transition-opacity">
                                                            Reject Request
                                                        </button>
                                                        <button type="button" @click="openReject = false" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                No pending ROI requests found.
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
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Showing {{ $logs->count() }} to {{ $logs->count() }} of {{ $logs->count() }} results
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
