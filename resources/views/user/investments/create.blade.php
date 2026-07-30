@extends('user.layouts.app')

@section('title', 'New Investment')

@section('content')
<div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">New Investment</h1>
        <p class="text-gray-600 mt-1">Submit a new investment request.</p>
    </div>

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
        <form action="{{ route('user.investments.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Investment Amount</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-medium">{{ get_setting('currency_symbol') ?? 'Rs.' }}</span>
                    </div>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1" step="0.01"
                        class="block w-full pr-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50 transition-colors"
                        style="padding-left: 3.5rem;"
                        placeholder="0.00">
                </div>
                <p class="mt-2 text-xs text-gray-500">Enter the exact amount you have transferred.</p>
            </div>

            <div>
                <label for="trx_id" class="block text-sm font-medium text-gray-700 mb-2">Transaction ID</label>
                <div class="relative">
                    <input type="text" name="trx_id" id="trx_id" value="{{ old('trx_id') }}" required
                        class="block w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-primary focus:border-primary sm:text-sm bg-gray-50/50 transition-colors"
                        placeholder="e.g. TXN123456789">
                </div>
                <p class="mt-2 text-xs text-gray-500">Enter the transaction ID or reference number from your payment.</p>
            </div>

            <div>
                <label for="payment_proof" class="block text-sm font-medium text-gray-700 mb-2">Payment Proof (Receipt/Screenshot)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50/50 hover:bg-gray-50 transition-colors group">
                    <div class="space-y-2 text-center">
                        <i class="ph ph-image text-4xl text-gray-400 group-hover:text-primary transition-colors"></i>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="payment_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary px-2 py-1 shadow-sm border border-gray-200">
                                <span>Upload a file</span>
                                <input id="payment_proof" name="payment_proof" type="file" class="hidden" required accept="image/jpeg,image/png,image/gif">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-xl font-medium shadow-sm hover:opacity-90 transition-opacity flex items-center gap-2">
                    <i class="ph ph-paper-plane-tilt"></i>
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
