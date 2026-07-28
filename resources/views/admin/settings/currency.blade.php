@extends('admin.layouts.app')

@section('title', 'Currency Settings')

@section('content')
<div class="min-h-full p-4 sm:p-6 space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.settings.admin') }}" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
            @include('admin.partials.icon', ['name' => 'arrow-left', 'size' => 18])
        </a>
        <h1 class="text-xl font-bold text-gray-800">Currency Settings</h1>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 text-green-700 rounded-xl text-sm font-medium border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
        <form action="{{ route('admin.settings.currency.update') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Currency Code</label>
                    <input type="text" name="code" value="{{ get_setting('currency_code', 'INR') }}" placeholder="e.g. USD, EUR, INR" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none focus:border-[var(--theme-primary)] focus:ring-1 focus:ring-[var(--theme-primary)] transition-all">
                    <p class="text-xs text-gray-500 mt-1.5">The 3-letter currency code (e.g. INR, USD).</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Currency Symbol</label>
                    <input type="text" name="symbol" value="{{ get_setting('currency_symbol', 'Rs') }}" placeholder="e.g. $, €, Rs" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none focus:border-[var(--theme-primary)] focus:ring-1 focus:ring-[var(--theme-primary)] transition-all">
                    <p class="text-xs text-gray-500 mt-1.5">The symbol for your currency (e.g. Rs, $).</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-[var(--theme-primary)] hover:opacity-90 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                        @include('admin.partials.icon', ['name' => 'save', 'size' => 16])
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
