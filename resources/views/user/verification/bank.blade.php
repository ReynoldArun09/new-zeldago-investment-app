@extends('user.layouts.app')

@section('title', 'Bank Verification')

@section('content')
<div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Verification</h1>
            <p class="text-gray-600 mt-1">Complete your verification to unlock all platform features.</p>
        </div>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('user.verification.kyc') }}" class="px-6 py-2.5 text-sm font-medium text-white hover:opacity-90 transition-opacity" style="background-color: #4ade80;">
            View KYC
        </a>
        <a href="{{ route('user.verification.nominee') }}" class="px-6 py-2.5 text-sm font-medium text-white hover:opacity-90 transition-opacity" style="background-color: #ef4444;">
            View Nominee
        </a>
        <a href="{{ route('user.verification.bank') }}" class="px-6 py-2.5 text-sm font-medium text-white hover:opacity-90 transition-opacity" style="background-color: #848b98;">
            View Bank Details
        </a>
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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($bankDetail && $bankDetail->status === 'PENDING')
            <div class="p-8">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-indigo-100">
                        <i class="ph ph-bank text-3xl text-indigo-500"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Please Confirm Your Bank Details</h3>
                    <p class="text-gray-600 mt-2">The administration has added your bank details. Please review and confirm them below.</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-6 rounded-xl border border-gray-100 mb-6 text-sm">
                    <div>
                        <span class="text-gray-500 block mb-1">Account Holder Name</span> 
                        <span class="font-medium text-gray-900 text-base">{{ $bankDetail->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-1">Bank Name</span> 
                        <span class="font-medium text-gray-900 text-base">{{ $bankDetail->bank_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-1">Account Number</span> 
                        <span class="font-medium text-gray-900 text-base">{{ $bankDetail->account_number }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-1">IFSC Code</span> 
                        <span class="font-medium text-gray-900 text-base">{{ $bankDetail->ifsc_code }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-1">UPI ID</span> 
                        <span class="font-medium text-gray-900 text-base">{{ $bankDetail->upi_id ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block mb-1">UPI Number</span> 
                        <span class="font-medium text-gray-900 text-base">{{ $bankDetail->upi_number ?? 'N/A' }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('user.verification.bank.confirm') }}" class="text-center">
                    @csrf
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl transition-colors shadow-sm inline-flex items-center gap-2">
                        <i class="ph ph-check-circle text-lg"></i> I Confirm These Details
                    </button>
                </form>
            </div>
        @elseif($bankDetail && $bankDetail->status === 'APPROVED')
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-green-100">
                    <i class="ph ph-shield-check text-3xl text-green-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Details Verified</h3>
                <p class="text-gray-600 mt-2">Your bank details have been approved. You can now make withdrawals.</p>
            </div>
        @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <i class="ph ph-bank text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">No Bank Details</h3>
                <p class="text-gray-600 mt-2">Your bank details are managed by the administration. If you need to update them, please contact support.</p>
            </div>
        @endif
    </div>
</div>
@endsection
