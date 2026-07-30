@extends('user.layouts.app')

@section('title', 'KYC Verification')

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
        @if($kyc && $kyc->status === 'pending')
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-100">
                    <i class="ph ph-hourglass-high text-3xl text-amber-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Verification Pending</h3>
                <p class="text-gray-600 mt-2">Your KYC documents have been submitted and are currently under review by the administration. You will be notified once they are approved.</p>
            </div>
        @elseif($kyc && $kyc->status === 'approved')
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-green-100">
                    <i class="ph ph-shield-check text-3xl text-green-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Identity Verified</h3>
                <p class="text-gray-600 mt-2">Your KYC documents have been approved. Your account is fully verified.</p>
            </div>
        @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <i class="ph ph-identification-card text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">No KYC Details</h3>
                <p class="text-gray-600 mt-2">Your KYC details are managed by the administration. If you need to update them, please contact support.</p>
            </div>
        @endif
    </div>
</div>
@endsection
