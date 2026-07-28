@extends('user.layouts.app')

@section('title', 'Nominee Verification')

@section('content')
<div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Nominee Details</h1>
        <p class="text-gray-600 mt-1">Provide your beneficiary details for your account investments.</p>
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
        @if($nominee && $nominee->status === 'pending')
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-amber-100">
                    <i class="ph ph-hourglass-high text-3xl text-amber-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Verification Pending</h3>
                <p class="text-gray-600 mt-2">Your nominee details have been submitted and are currently under review by the administration. You will be notified once they are approved.</p>
            </div>
        @elseif($nominee && $nominee->status === 'approved')
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-green-100">
                    <i class="ph ph-shield-check text-3xl text-green-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Nominee Verified</h3>
                <p class="text-gray-600 mt-2">Your beneficiary has been approved.</p>
                
                <div class="mt-6 text-left p-4 bg-gray-50 rounded-xl border border-gray-100 max-w-md mx-auto">
                    <p class="text-sm text-gray-500">Nominee Name</p>
                    <p class="font-medium text-gray-900 mb-3">{{ $nominee->name }}</p>
                    
                    <p class="text-sm text-gray-500">Relation</p>
                    <p class="font-medium text-gray-900">{{ $nominee->relation }}</p>
                </div>
            </div>
        @else
            @if($nominee && $nominee->status === 'rejected')
                <div class="p-6 bg-red-50 border-b border-red-100 flex items-start gap-3">
                    <i class="ph ph-warning-circle text-red-600 text-xl shrink-0"></i>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Your previous submission was rejected</h3>
                        @if($nominee->admin_message)
                            <p class="text-sm text-red-700 mt-1">Reason: {{ $nominee->admin_message }}</p>
                        @endif
                        <p class="text-sm text-red-700 mt-2">Please submit valid details to try again.</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('user.verification.nominee.submit') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nominee Full Name</label>
                        <input type="text" name="name" id="name" required
                            class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50 transition-colors">
                    </div>

                    <div>
                        <label for="relation" class="block text-sm font-medium text-gray-700 mb-2">Relation with you</label>
                        <select name="relation" id="relation" required class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50 transition-colors">
                            <option value="">Select Relation...</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Child">Child</option>
                            <option value="Parent">Parent</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="identity_front_proof" class="block text-sm font-medium text-gray-700 mb-2">Identity Proof (Front Side)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50/50 hover:bg-gray-50 transition-colors group">
                            <div class="space-y-2 text-center">
                                <i class="ph ph-identification-card text-4xl text-gray-400 group-hover:text-primary transition-colors"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="identity_front_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary px-2 py-1 shadow-sm border border-gray-200">
                                        <span>Upload a file</span>
                                        <input id="identity_front_proof" name="identity_front_proof" type="file" class="hidden" required accept="image/jpeg,image/png,image/gif">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="identity_back_proof" class="block text-sm font-medium text-gray-700 mb-2">Identity Proof (Back Side)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50/50 hover:bg-gray-50 transition-colors group">
                            <div class="space-y-2 text-center">
                                <i class="ph ph-identification-card text-4xl text-gray-400 group-hover:text-primary transition-colors"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="identity_back_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary px-2 py-1 shadow-sm border border-gray-200">
                                        <span>Upload a file</span>
                                        <input id="identity_back_proof" name="identity_back_proof" type="file" class="hidden" required accept="image/jpeg,image/png,image/gif">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500 text-center">Please provide a valid ID for your nominee.</p>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl font-medium shadow-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                        <i class="ph ph-paper-plane-tilt"></i>
                        Submit Nominee
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
