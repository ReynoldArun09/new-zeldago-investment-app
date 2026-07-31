@extends('admin.layouts.app')

@section('title', 'Review Bank Details')

@section('content')
<div class="w-full max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-800">Review Bank Details</h2>
        <a href="{{ route('admin.verification.bank') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <i class="ph ph-arrow-left"></i> Back to List
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3">
            <i class="ph ph-check-circle text-green-600 text-xl shrink-0"></i>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3">
            <i class="ph ph-warning-circle text-red-600 text-xl shrink-0"></i>
            <div class="text-sm font-medium text-red-800">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- User Info --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5">
                <h3 class="font-bold text-gray-800 mb-4 pb-3 border-b border-gray-100">User Information</h3>
                
                <div class="flex items-center gap-4 mb-5">
                    @if($bank->user->profile_image)
                        <img src="{{ Storage::url($bank->user->profile_image) }}" alt="Profile" class="w-12 h-12 rounded-full object-cover border border-gray-200">
                    @else
                        <div class="w-12 h-12 rounded-full bg-[var(--theme-primary)]/10 text-[var(--theme-primary)] flex items-center justify-center font-bold text-lg border border-[var(--theme-primary)]/20">
                            {{ substr($bank->user->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <div class="font-bold text-gray-900">{{ $bank->user->name }}</div>
                        <a href="{{ route('admin.users.details', $bank->user->username) }}" class="text-sm text-[var(--theme-primary)] hover:underline">{{ '@' . $bank->user->username }}</a>
                    </div>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Email</span>
                        <span class="font-medium text-gray-800">{{ $bank->user->email }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-gray-50">
                        <span class="text-gray-500">Phone</span>
                        <span class="font-medium text-gray-800">{{ $bank->user->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-gray-500">Account Type</span>
                        <span class="font-medium text-gray-800 capitalize">{{ $bank->user->account_type ?? 'Investor' }}</span>
                    </div>
                </div>
            </div>

            {{-- Action Box --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5">
                <h3 class="font-bold text-gray-800 mb-4 pb-3 border-b border-gray-100">Update Status</h3>
                
                <form action="{{ route('admin.verification.bank.status', $bank->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Status</label>
                        @if(strtolower($bank->status) === 'pending')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">Pending</span>
                        @elseif(strtolower($bank->status) === 'approved')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">Approved</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-600 border border-red-200">Rejected</span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status-select" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[var(--theme-primary)] focus:bg-white transition-colors" required>
                            <option value="">Select Status...</option>
                            <option value="APPROVED" {{ strtoupper($bank->status) === 'APPROVED' ? 'selected' : '' }}>Approve</option>
                            <option value="REJECTED" {{ strtoupper($bank->status) === 'REJECTED' ? 'selected' : '' }}>Reject</option>
                        </select>
                    </div>

                    <div id="rejection-reason" class="mb-5 {{ strtoupper($bank->status) === 'REJECTED' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason</label>
                        <textarea name="admin_message" rows="3" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[var(--theme-primary)] focus:bg-white transition-colors" placeholder="Required if rejecting...">{{ $bank->rejection_reason }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-[var(--theme-primary)] text-white font-medium rounded-lg hover:opacity-90 transition-opacity">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        {{-- Details --}}
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-5 pb-3 border-b border-gray-100">Bank Details</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                    <div>
                        <div class="text-gray-500 mb-1">Account Holder Name</div>
                        <div class="font-semibold text-gray-900 bg-gray-50 p-2.5 rounded-lg border border-gray-100">{{ $bank->name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 mb-1">Bank Name</div>
                        <div class="font-semibold text-gray-900 bg-gray-50 p-2.5 rounded-lg border border-gray-100">{{ $bank->bank_name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 mb-1">Account Number</div>
                        <div class="font-semibold text-gray-900 bg-gray-50 p-2.5 rounded-lg border border-gray-100">{{ $bank->account_number ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500 mb-1">IFSC Code</div>
                        <div class="font-semibold text-gray-900 bg-gray-50 p-2.5 rounded-lg border border-gray-100">{{ $bank->ifsc_code ?? 'N/A' }}</div>
                    </div>
                    
                    @if($bank->upi_id)
                    <div>
                        <div class="text-gray-500 mb-1">UPI ID</div>
                        <div class="font-semibold text-gray-900 bg-gray-50 p-2.5 rounded-lg border border-gray-100">{{ $bank->upi_id }}</div>
                    </div>
                    @endif
                    
                    @if($bank->upi_number)
                    <div>
                        <div class="text-gray-500 mb-1">UPI Number</div>
                        <div class="font-semibold text-gray-900 bg-gray-50 p-2.5 rounded-lg border border-gray-100">{{ $bank->upi_number }}</div>
                    </div>
                    @endif
                </div>

                @if($bank->proof_image)
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <div class="text-gray-500 mb-3 font-medium">Document / Proof Image</div>
                        <div class="border-2 border-dashed border-gray-200 rounded-xl p-2 bg-gray-50 text-center">
                            @if(Str::endsWith(strtolower($bank->proof_image), ['.pdf']))
                                <a href="{{ Storage::url($bank->proof_image) }}" target="_blank" class="inline-flex flex-col items-center justify-center p-8 text-gray-500 hover:text-[var(--theme-primary)] transition-colors">
                                    <i class="ph ph-file-pdf text-4xl mb-2 text-red-500"></i>
                                    <span class="font-medium">View PDF Document</span>
                                </a>
                            @else
                                <a href="{{ Storage::url($bank->proof_image) }}" target="_blank" class="block">
                                    <img src="{{ Storage::url($bank->proof_image) }}" alt="Bank Proof" class="max-w-full max-h-[500px] object-contain rounded-lg mx-auto shadow-sm">
                                </a>
                                <div class="mt-2 text-xs text-gray-400">Click image to view full size</div>
                            @endif
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('status-select').addEventListener('change', function() {
        const reasonBox = document.getElementById('rejection-reason');
        const reasonInput = reasonBox.querySelector('textarea');
        
        if (this.value === 'REJECTED' || this.value === 'rejected') {
            reasonBox.classList.remove('hidden');
            reasonInput.setAttribute('required', 'required');
        } else {
            reasonBox.classList.add('hidden');
            reasonInput.removeAttribute('required');
        }
    });
</script>
@endsection
