@extends('admin.layouts.app')

@section('title', 'Pending Withdrawals')

@section('content')
<div class="min-h-full p-4 sm:p-6 space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-700">Pending Withdrawals</h1>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-sm flex flex-col rounded-none">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-white" style="background-color: var(--theme-primary);">
                        <th class="text-left px-5 py-3 font-medium">User</th>
                        <th class="text-left px-5 py-3 font-medium">Payout Method</th>
                        <th class="text-left px-5 py-3 font-medium">Details</th>
                        <th class="text-right px-5 py-3 font-medium">Amount</th>
                        <th class="text-right px-5 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($withdrawals as $withdrawal)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ $withdrawal->user->name ?? 'N/A' }}</p>
                            <p class="text-xs" style="color: var(--theme-primary);">{{ '@' . ($withdrawal->user->username ?? '') }}</p>
                        </td>
                        <td class="px-5 py-3 font-medium text-gray-600">
                            {{ $withdrawal->payout_method }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500 max-w-xs truncate">
                            {{ $withdrawal->payout_details }}
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-red-600">
                            {{ format_currency($withdrawal->amount) }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2" x-data="{ openApprove: false, openReject: false }">
                                <button type="button" @click="openApprove = true" class="px-3 py-1 text-white bg-[#00A843] hover:bg-green-700 rounded text-[11px] font-medium transition-colors">
                                    Approve
                                </button>
                                <button type="button" @click="openReject = true" class="px-3 py-1 text-white bg-[#E2000F] hover:bg-red-700 rounded text-[11px] font-medium transition-colors">
                                    Reject
                                </button>

                                <!-- Approve Modal -->
                                <div x-show="openApprove" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div x-show="openApprove" @click="openApprove = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                                            <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                                        </div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="openApprove" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                                            <form action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="bg-white px-6 pt-5 pb-4">
                                                    <h3 class="text-xl font-bold text-gray-800 mb-4 text-left">
                                                        Approve Withdrawal
                                                    </h3>
                                                    <hr class="border-gray-100 mb-4 -mx-6">
                                                    
                                                    <div class="space-y-4 text-left">
                                                        <div>
                                                            <label class="block text-sm text-gray-600 mb-1">Transaction ID</label>
                                                            <input type="text" name="trx_id" required class="w-full rounded-lg border-gray-300 focus:border-[var(--theme-primary)] text-sm p-3" placeholder="Enter Transaction ID or Hash">
                                                        </div>
                                                        <div>
                                                            <label class="block text-sm text-gray-600 mb-1">Proof of Payment (Screenshot)</label>
                                                            <input type="file" name="proof_image" accept="image/*" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-white px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                                                    <button type="submit" class="inline-flex justify-center rounded-lg px-4 py-2 bg-[#00A843] text-sm font-medium text-white hover:opacity-90">
                                                        Submit & Approve
                                                    </button>
                                                    <button type="button" @click="openApprove = false" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
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
                                        <div x-show="openReject" @click="openReject = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                                            <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                                        </div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                        <div x-show="openReject" class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                                            <form action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}" method="POST">
                                                @csrf
                                                <div class="bg-white px-6 pt-5 pb-4">
                                                    <h3 class="text-xl font-bold text-gray-800 mb-4 text-left">
                                                        Reject Withdrawal
                                                    </h3>
                                                    <p class="text-xs text-red-600 mb-3 text-left">This will refund {{ format_currency($withdrawal->amount) }} back to the user's wallet.</p>
                                                    <hr class="border-gray-100 mb-4 -mx-6">
                                                    <div class="space-y-4 text-left">
                                                        <div>
                                                            <label class="block text-sm text-gray-600 mb-1">Rejection Note</label>
                                                            <textarea name="reject_note" required rows="3" class="w-full rounded-lg border-gray-300 focus:border-red-500 text-sm p-3 outline-none" placeholder="Explain why this withdrawal is rejected..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-white px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                                                    <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-[#E2000F] text-sm font-medium text-white hover:bg-red-700">
                                                        Reject & Refund
                                                    </button>
                                                    <button type="button" @click="openReject = false" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
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
                        <td colspan="5" class="px-5 py-8 text-center text-xs text-gray-400">No pending withdrawals</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($withdrawals->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $withdrawals->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
