@extends('admin.layouts.app')

@section('title', 'KYC Review')

@section('content')
<div class="w-full">
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.verification.kyc') }}" class="text-[var(--theme-primary)] hover:underline flex items-center gap-1 font-medium text-sm">
            <i class="ph ph-arrow-left"></i> Back
        </a>
        <h1 class="text-xl font-bold text-gray-800">KYC Review</h1>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Card 1: User Info --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden h-fit">
            <div class="p-5 border-b border-gray-100 flex items-center gap-2">
                <i class="ph ph-user text-[var(--theme-primary)] text-lg"></i>
                <h3 class="font-bold text-gray-800">User Info</h3>
            </div>
            <div class="p-5">
                <ul class="space-y-4 text-sm">
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Full Name</span>
                        <span class="font-medium text-gray-900">{{ $kyc->user->name }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Username</span>
                        <a href="{{ route('admin.users.details', $kyc->user->username) }}" class="font-medium text-[var(--theme-primary)] hover:underline">{{ '@' . $kyc->user->username }}</a>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">User Type</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($kyc->user->account_type ?? 'Investor') }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Country</span>
                        <span class="font-medium text-gray-900">{{ $kyc->country ?? '---' }}</span>
                    </li>
                    <li class="flex justify-between gap-4">
                        <span class="text-gray-500 whitespace-nowrap">Address</span>
                        <span class="font-medium text-gray-900 text-right">{{ $kyc->address ?? '---' }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Submitted</span>
                        <span class="font-medium text-gray-900">{{ $kyc->created_at->format('M d Y, H:i') }}</span>
                    </li>
                    <li class="flex items-center justify-between pt-2 border-t border-gray-50">
                        <span class="text-gray-500">Status</span>
                        @if($kyc->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-amber-500 border border-amber-300">
                                Pending
                            </span>
                        @elseif($kyc->status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-emerald-500 border border-emerald-300">
                                Approved
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-red-500 border border-red-300">
                                Rejected
                            </span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        {{-- Card 2: Document Info --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden h-fit">
            <div class="p-5 border-b border-gray-100 flex items-center gap-2">
                <i class="ph ph-file-text text-[var(--theme-primary)] text-lg"></i>
                <h3 class="font-bold text-gray-800">Document Info</h3>
            </div>
            <div class="p-5">
                <ul class="space-y-4 text-sm mb-6">
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Document Type</span>
                        <span class="font-medium text-gray-900">{{ $kyc->document_type }}</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <span class="text-gray-500">Document Number</span>
                        <span class="font-medium text-gray-900">{{ $kyc->document_number ?? '---' }}</span>
                    </li>
                </ul>

                @if($kyc->status === 'pending')
                    <div class="flex flex-col gap-3">
                        <form action="{{ route('admin.verification.kyc.status', $kyc->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-[#00A843] text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                                <i class="ph ph-check-circle text-lg"></i> Approve KYC
                            </button>
                        </form>
                        <form action="{{ route('admin.verification.kyc.status', $kyc->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="button" onclick="const msg = prompt('Enter rejection reason (optional):'); if(msg !== null) { this.nextElementSibling.value = msg; this.closest('form').submit(); }" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 text-red-500 border border-red-200 rounded-lg font-medium hover:bg-red-100 transition-colors">
                                <i class="ph ph-x-circle text-lg"></i> Reject KYC
                            </button>
                            <input type="hidden" name="admin_message" value="">
                        </form>
                    </div>
                @else
                    <div class="p-4 rounded-lg border {{ $kyc->status === 'approved' ? 'bg-green-50 border-green-100 text-green-700' : 'bg-red-50 border-red-100 text-red-700' }}">
                        <p class="font-medium text-sm flex items-center gap-2 mb-1">
                            <i class="ph {{ $kyc->status === 'approved' ? 'ph-check-circle' : 'ph-x-circle' }}"></i> 
                            KYC is already {{ $kyc->status }}.
                        </p>
                        @if($kyc->admin_message)
                            <p class="text-xs opacity-90 mt-1">Reason: {{ $kyc->admin_message }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 3: Document Images --}}
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden h-fit">
            <div class="p-5 border-b border-gray-100 flex items-center gap-2">
                <i class="ph ph-image text-[var(--theme-primary)] text-lg"></i>
                <h3 class="font-bold text-gray-800">Document Images</h3>
            </div>
            <div class="p-5">
                <div class="space-y-6">
                    <div>
                        <span class="block text-sm text-gray-500 mb-2">Front Side</span>
                        @if($kyc->document_front_proof)
                            <a href="{{ Storage::url($kyc->document_front_proof) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:border-[var(--theme-primary)] transition-colors">
                                <img src="{{ Storage::url($kyc->document_front_proof) }}" alt="Front Side" class="w-full object-cover max-h-48">
                            </a>
                        @else
                            <div class="p-4 bg-gray-50 border border-gray-100 rounded-lg text-center text-gray-400 text-sm">
                                No image provided
                            </div>
                        @endif
                    </div>

                    <div>
                        <span class="block text-sm text-gray-500 mb-2">Back Side</span>
                        @if($kyc->document_back_proof)
                            <a href="{{ Storage::url($kyc->document_back_proof) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:border-[var(--theme-primary)] transition-colors">
                                <img src="{{ Storage::url($kyc->document_back_proof) }}" alt="Back Side" class="w-full object-cover max-h-48">
                            </a>
                        @else
                            <div class="p-4 bg-gray-50 border border-gray-100 rounded-lg text-center text-gray-400 text-sm">
                                No image provided
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
