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
                    <div class="flex flex-col sm:flex-row gap-3">
                        <form action="{{ route('admin.investments.approve', $investment->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-[#00A843] text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                                <i class="ph ph-check-circle text-lg"></i> Approve Investment
                            </button>
                        </form>
                        <form action="{{ route('admin.investments.reject', $investment->id) }}" method="POST" class="w-full">
                            @csrf
                            <button type="button" onclick="const msg = prompt('Enter rejection reason (optional):'); if(msg !== null) { this.nextElementSibling.value = msg; this.closest('form').submit(); }" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-500 border border-red-200 rounded-lg font-medium hover:bg-red-100 transition-colors">
                                <i class="ph ph-x-circle text-lg"></i> Reject Investment
                            </button>
                            <input type="hidden" name="admin_message" value="">
                        </form>
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
