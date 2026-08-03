@extends('admin.layouts.app')

@section('title', 'Investment Details')

@section('content')
<div class="w-full">
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.investments.index') }}" class="text-[var(--theme-primary)] hover:underline flex items-center gap-1 font-medium text-sm">
            <i class="ph ph-arrow-left"></i> Back
        </a>
        <h1 class="text-xl font-bold text-gray-800">Investment Details</h1>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3">
            <i class="ph ph-check-circle text-green-600 text-xl shrink-0"></i>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
            <ul class="text-sm text-red-800 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        
        {{-- Card 1: User & Investment Info --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden h-fit">
            <div class="p-5 border-b border-gray-100 flex items-center gap-2">
                <i class="ph ph-info text-[var(--theme-primary)] text-lg"></i>
                <h3 class="font-bold text-gray-800">User & Investment Info</h3>
            </div>
            <div class="p-5">
                <ul class="space-y-5 text-sm">
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Transaction ID</span>
                        <span class="font-bold text-gray-900">{{ $investment->trx_id }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Status</span>
                        @if(strtoupper($investment->status) === 'PENDING')
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-amber-500 border border-amber-400 uppercase">
                                Pending
                            </span>
                        @elseif(strtoupper($investment->status) === 'ACTIVE')
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-indigo-500 border border-indigo-400 uppercase">
                                Active
                            </span>
                        @elseif(strtoupper($investment->status) === 'COMPLETED')
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-emerald-500 border border-emerald-400 uppercase">
                                Completed
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-gray-500 border border-gray-400 uppercase">
                                {{ $investment->status }}
                            </span>
                        @endif
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Full Name</span>
                        <span class="font-bold text-gray-900">{{ $investment->user->name ?? 'Unknown User' }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Username</span>
                        <a href="{{ $investment->user && $investment->user->username ? route('admin.users.details', $investment->user->username) : '#' }}" class="font-medium text-[var(--theme-primary)] hover:underline">{{ $investment->user && $investment->user->username ? '@' . $investment->user->username : '@unknown' }}</a>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Sponsor By</span>
                        @if($investment->user && $investment->user->sponsor)
                            <a href="{{ route('admin.users.details', $investment->user->sponsor->username) }}" class="font-medium text-[var(--theme-primary)] hover:underline">
                                {{ '@' . $investment->user->sponsor->username }}
                            </a>
                        @else
                            <span class="font-bold text-gray-900">Admin</span>
                        @endif
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Email</span>
                        <span class="font-medium text-[var(--theme-primary)]">{{ $investment->user->email ?? 'N/A' }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Initial Deposit</span>
                        <span class="font-bold text-gray-900">{{ format_currency($investment->amount) }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Start Date</span>
                        <span class="font-bold text-gray-900">{{ $investment->created_at->format('m/d/Y H:i') }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">End Date</span>
                        <span class="font-bold text-gray-900">-</span>
                    </li>
                </ul>
                
                @if($investment->user)
                <div class="mt-6 pt-5 border-t border-gray-100 flex justify-end">
                    <div x-data="{ showContractModal: false, contractDate: '{{ $investment->user->contract_date ? \Carbon\Carbon::parse($investment->user->contract_date)->format('Y-m-d') : '' }}' }">
                        <button type="button" @click="showContractModal = true"
                            class="flex items-center gap-1.5 text-sm font-medium text-white px-4 py-2 rounded-lg transition-colors bg-purple-500 hover:bg-purple-600 opacity-80">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Contract
                        </button>

                        <div x-show="showContractModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
                            <div @click.outside="showContractModal = false" class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform transition-all border border-gray-200">
                                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-purple-50/50 text-left">
                                    <h3 class="text-base font-semibold text-purple-900 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Contract Details
                                    </h3>
                                    <button type="button" @click="showContractModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                                <form method="POST" action="{{ route('admin.users.contract', $investment->user->username ?? $investment->user->id) }}">
                                    @csrf
                                    <div class="px-5 py-6 text-gray-600 text-sm text-left">
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Contract Date</label>
                                                    <input type="date" name="contract_date" x-model="contractDate" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500 transition-colors">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Notify User Date</label>
                                                    <input type="date" name="contract_notify_date" :max="contractDate" value="{{ $investment->user->contract_notify_date ? \Carbon\Carbon::parse($investment->user->contract_notify_date)->format('Y-m-d') : '' }}" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-purple-500 transition-colors">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-5 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-2">
                                        <button type="button" @click="showContractModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-500 border border-transparent rounded-lg shadow-sm hover:bg-purple-600 transition-colors">
                                            Save Contract
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Card 2: Payment Proof & Actions --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden h-fit">
            <div class="p-5 border-b border-gray-100 flex items-center gap-2">
                <i class="ph ph-image text-[var(--theme-primary)] text-lg"></i>
                <h3 class="font-bold text-gray-800">Payment Proof</h3>
            </div>
            <div class="p-5">
                <div class="mb-6">
                    @if($investment->payment_proof)
                        <a href="{{ Storage::url($investment->payment_proof) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:border-[var(--theme-primary)] transition-colors">
                            <img src="{{ Storage::url($investment->payment_proof) }}" alt="Payment Proof" class="w-full object-cover max-h-80">
                        </a>
                    @else
                        <div class="p-8 bg-gray-50 border border-gray-100 rounded-lg text-center text-gray-400 text-sm">
                            <i class="ph ph-image-broken text-4xl mb-2 text-gray-300"></i><br>
                            No payment proof provided
                        </div>
                    @endif
                </div>

                @if(strtoupper($investment->status) === 'PENDING')
                    <div class="flex flex-col sm:flex-row gap-3" x-data="{ showApprove: false, showReject: false, rejectReason: '' }">
                        <!-- Approve Button -->
                        <button type="button" @click="showApprove = true" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-[#00A843] text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                            <i class="ph ph-check-circle text-lg"></i> Approve Investment
                        </button>
                        
                        <!-- Reject Button -->
                        <button type="button" @click="showReject = true" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-500 border border-red-200 rounded-lg font-medium hover:bg-red-100 transition-colors">
                            <i class="ph ph-x-circle text-lg"></i> Reject Investment
                        </button>

                        <!-- Approve Modal -->
                        <div x-show="showApprove" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="showApprove" @click="showApprove = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div x-show="showApprove" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                                    <form action="{{ route('admin.investments.approve', $investment->id) }}" method="POST">
                                        @csrf
                                        <div class="bg-white px-6 pt-5 pb-4 text-center">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                                <i class="ph ph-check-circle text-2xl text-green-600"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Approve Investment</h3>
                                            <p class="text-sm text-gray-500">Are you sure you want to approve this investment? This action cannot be undone.</p>
                                        </div>
                                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-[#00A843] text-sm font-medium text-white hover:bg-green-700 sm:w-auto transition-colors">
                                                Confirm Approval
                                            </button>
                                            <button type="button" @click="showApprove = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div x-show="showReject" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                <div x-show="showReject" @click="showReject = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/75 backdrop-blur-sm" aria-hidden="true"></div>
                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                <div x-show="showReject" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                                    <form action="{{ route('admin.investments.reject', $investment->id) }}" method="POST">
                                        @csrf
                                        <div class="bg-white px-6 pt-5 pb-4">
                                            <div class="flex items-center gap-3 mb-4 text-red-600">
                                                <i class="ph ph-x-circle text-2xl"></i>
                                                <h3 class="text-lg font-medium text-gray-900">Reject Investment</h3>
                                            </div>
                                            <p class="text-sm text-gray-500 mb-4">Please provide a reason for rejecting this investment (optional):</p>
                                            <textarea name="admin_message" x-model="rejectReason" class="w-full rounded-none border-gray-300 focus:border-gray-500 focus:ring-gray-500 text-sm" rows="3" placeholder="Enter rejection reason..."></textarea>
                                        </div>
                                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-red-600 text-sm font-medium text-white hover:bg-red-700 sm:w-auto transition-colors">
                                                Confirm Rejection
                                            </button>
                                            <button type="button" @click="showReject = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto transition-colors">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                @else
                    <div class="p-4 rounded-lg border {{ in_array(strtoupper($investment->status), ['ACTIVE', 'COMPLETED']) ? 'bg-green-50 border-green-100 text-green-700' : 'bg-red-50 border-red-100 text-red-700' }}">
                        <p class="font-medium text-sm flex items-center gap-2">
                            <i class="ph {{ in_array(strtoupper($investment->status), ['ACTIVE', 'COMPLETED']) ? 'ph-check-circle' : 'ph-info' }}"></i> 
                            Investment is {{ strtoupper($investment->status) }}.
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
