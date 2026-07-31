@extends('user.layouts.app')

@section('title', 'My Active Investments')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Investments</h1>
            <p class="text-gray-600 mt-1">View your pending and active investments.</p>
        </div>

    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3">
            <i class="ph ph-check-circle text-green-600 text-xl shrink-0"></i>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3">
            <i class="ph ph-x-circle text-red-600 text-xl shrink-0"></i>
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-none shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead>
                    <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                        <th class="px-6 py-4 font-medium">Transaction ID</th>
                        <th class="px-6 py-4 font-medium">Amount</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium text-center">Proof</th>
                        <th class="px-6 py-4 font-medium text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($investments as $inv)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $inv->trx_id }}</td>
                            <td class="px-6 py-4 font-medium">{{ format_currency($inv->amount) }}</td>
                            <td class="px-6 py-4">
                                @if($inv->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ph ph-clock text-amber-500"></i> Pending
                                    </span>
                                @elseif(strtoupper($inv->status) === 'ACTIVE')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                        <i class="ph ph-check-circle text-green-500"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                                        {{ ucfirst($inv->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $inv->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($inv->payment_proof)
                                    <a href="{{ Storage::url($inv->payment_proof) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:text-primary hover:bg-indigo-50 transition-colors">
                                        <i class="ph ph-image text-lg"></i>
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if(strtoupper($inv->status) === 'ACTIVE')
                                    <form id="close-form-{{ $inv->id }}" action="{{ route('user.investments.close', $inv->id) }}" method="POST">
                                        @csrf
                                        <button type="button" onclick="confirmClose({{ $inv->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition-colors border border-red-100">
                                            <i class="ph ph-x-square"></i> Close
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="ph ph-folder-open text-4xl text-gray-300 mb-3"></i>
                                    <p class="font-medium text-gray-600">No active investments found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($investments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $investments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmClose(id) {
        Swal.fire({
            title: 'Close Investment',
            text: 'Are you sure you want to close this investment? This will stop further ROI generation and submit a close request to the admin. This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, close it!',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'px-4 py-2 rounded-xl',
                cancelButton: 'px-4 py-2 rounded-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('close-form-' + id).submit();
            }
        })
    }
</script>
@endpush
