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
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-100">
                    <i class="ph ph-hourglass-high text-3xl text-amber-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Verification Pending</h3>
                <p class="text-gray-600 mt-2">Your bank details have been submitted and are currently under review by the administration.</p>
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
            @if($bankDetail && $bankDetail->status === 'REJECTED')
                <div class="p-6 bg-red-50 border-b border-red-100 flex items-start gap-3">
                    <i class="ph ph-warning-circle text-red-600 text-xl shrink-0"></i>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Your previous submission was rejected</h3>
                        <p class="text-sm text-red-700 mt-1">Reason: {{ $bankDetail->rejection_reason ?? 'No reason provided.' }}</p>
                        <p class="text-sm text-red-700 mt-2">Please submit your details again carefully.</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('user.verification.bank.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 sm:p-8 space-y-8">
                    
                    {{-- Bank Details Section --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Bank Details</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Holder Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $bankDetail->name ?? '') }}" required class="block w-full rounded-xl border border-gray-200 px-4 py-3 shadow-sm focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)] sm:text-sm outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name <span class="text-red-500">*</span></label>
                                <input type="text" name="bank_name" value="{{ old('bank_name', $bankDetail->bank_name ?? '') }}" required class="block w-full rounded-xl border border-gray-200 px-4 py-3 shadow-sm focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)] sm:text-sm outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Number <span class="text-red-500">*</span></label>
                                <input type="text" name="account_number" value="{{ old('account_number', $bankDetail->account_number ?? '') }}" required class="block w-full rounded-xl border border-gray-200 px-4 py-3 shadow-sm focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)] sm:text-sm outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">IFSC / Branch Code <span class="text-red-500">*</span></label>
                                <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $bankDetail->ifsc_code ?? '') }}" required class="block w-full rounded-xl border border-gray-200 px-4 py-3 shadow-sm focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)] sm:text-sm outline-none transition-colors">
                            </div>
                        </div>
                    </div>

                    {{-- UPI Details Section --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">UPI Details <span class="text-sm font-normal text-gray-500">(Optional)</span></h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">UPI ID</label>
                                <input type="text" name="upi_id" value="{{ old('upi_id', $bankDetail->upi_id ?? '') }}" placeholder="e.g. name@bank" class="block w-full rounded-xl border border-gray-200 px-4 py-3 shadow-sm focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)] sm:text-sm outline-none transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">UPI Number</label>
                                <input type="text" name="upi_number" value="{{ old('upi_number', $bankDetail->upi_number ?? '') }}" class="block w-full rounded-xl border border-gray-200 px-4 py-3 shadow-sm focus:border-[var(--primary)] focus:ring-1 focus:ring-[var(--primary)] sm:text-sm outline-none transition-colors">
                            </div>
                        </div>
                    </div>

                    {{-- Proof Image --}}
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Verification Proof</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Passbook / Cancelled Cheque / Statement Image</label>
                            <input type="file" name="proof_image" accept="image/*" class="block w-full text-sm text-gray-600 border border-gray-300 rounded-xl p-2.5 bg-gray-50 hover:bg-gray-100 cursor-pointer focus:outline-none focus:border-[var(--primary)] transition-colors file:mr-4 file:py-1 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700">
                            <p class="mt-2 text-xs text-gray-500">Supported formats: JPG, PNG, GIF. Max size: 2MB.</p>
                        </div>
                    </div>

                </div>

                <div class="bg-gray-50 px-6 py-4 sm:px-8 border-t border-gray-100 flex items-center justify-end">
                    <button type="submit" class="inline-flex justify-center items-center gap-2 rounded-xl border border-transparent bg-[var(--primary)] px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 focus:outline-none transition">
                        Submit Details
                        <i class="ph ph-arrow-right"></i>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
