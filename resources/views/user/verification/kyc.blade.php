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
            @if($kyc && $kyc->status === 'rejected')
                <div class="p-6 bg-red-50 border-b border-red-100 flex items-start gap-3">
                    <i class="ph ph-warning-circle text-red-600 text-xl shrink-0"></i>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Your previous submission was rejected</h3>
                        @if($kyc->admin_message)
                            <p class="text-sm text-red-700 mt-1">Reason: {{ $kyc->admin_message }}</p>
                        @endif
                        <p class="text-sm text-red-700 mt-2">Please upload a valid document to try again.</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('user.verification.kyc.submit') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">Document Type</label>
                        <select name="document_type" id="document_type" required class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50 transition-colors">
                            <option value="">Select Document Type...</option>
                            <option value="National ID">National ID</option>
                            <option value="Passport">Passport</option>
                            <option value="Driver's License">Driver's License</option>
                        </select>
                    </div>
                    <div>
                        <label for="document_number" class="block text-sm font-medium text-gray-700 mb-2">Document Number</label>
                        <input type="text" name="document_number" id="document_number" required class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50 transition-colors" placeholder="e.g. 123456789">
                    </div>
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <input type="text" name="country" id="country" required class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50 transition-colors" placeholder="e.g. United States">
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Full Address</label>
                        <input type="text" name="address" id="address" required class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50 transition-colors" placeholder="e.g. 123 Main St, City, State, Zip">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="document_front_proof" class="block text-sm font-medium text-gray-700 mb-2">Front Side Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50/50 hover:bg-gray-50 transition-colors group">
                            <div class="space-y-2 text-center">
                                <i class="ph ph-identification-card text-4xl text-gray-400 group-hover:text-primary transition-colors"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="document_front_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary px-2 py-1 shadow-sm border border-gray-200">
                                        <span>Upload a file</span>
                                        <input id="document_front_proof" name="document_front_proof" type="file" class="hidden" required accept="image/jpeg,image/png,image/gif">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="document_back_proof" class="block text-sm font-medium text-gray-700 mb-2">Back Side Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50/50 hover:bg-gray-50 transition-colors group">
                            <div class="space-y-2 text-center">
                                <i class="ph ph-identification-card text-4xl text-gray-400 group-hover:text-primary transition-colors"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="document_back_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary px-2 py-1 shadow-sm border border-gray-200">
                                        <span>Upload a file</span>
                                        <input id="document_back_proof" name="document_back_proof" type="file" class="hidden" required accept="image/jpeg,image/png,image/gif">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <p class="mt-2 text-xs text-gray-500 text-center">Please ensure the images are clear and all text is readable.</p>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl font-medium shadow-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                        <i class="ph ph-upload-simple"></i>
                        Submit KYC
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
