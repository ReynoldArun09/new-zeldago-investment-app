@extends('user.layouts.app')

@section('title', 'Create Investment')

@section('content')
<div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Create Investment</h1>
        <p class="text-gray-600 mt-1">Submit a new investment request.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
            <ul class="list-disc pl-5 text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('user.investments.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
            <input type="number" step="0.01" name="amount" required
                class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[var(--primary)] focus:border-[var(--primary)] outline-none transition-colors"
                placeholder="Enter amount">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID</label>
            <input type="text" name="trx_id" required
                class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[var(--primary)] focus:border-[var(--primary)] outline-none transition-colors"
                placeholder="Enter transaction ID">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Proof</label>
            <input type="file" name="payment_proof" accept="image/*" required
                class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[var(--primary)] focus:border-[var(--primary)] outline-none transition-colors bg-gray-50 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-[var(--primary)] hover:file:bg-gray-200">
            <p class="mt-1 text-xs text-gray-500">Allowed image type: jpeg, png, jpg, gif, webp (Max: 2MB).</p>
        </div>
        
        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('user.investments.active') }}"
                class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--primary)] transition-colors">
                Cancel
            </a>
            <button type="submit"
                class="px-5 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--primary)] transition-colors"
                style="background-color: var(--primary);">
                Submit Investment
            </button>
        </div>
    </form>
</div>
@endsection
