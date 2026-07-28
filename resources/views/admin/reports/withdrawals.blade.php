@extends('admin.layouts.app')

@section('title', 'Withdrawals Report')

@section('content')
<div class="w-full">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Withdrawals Report</h1>
    </div>

    {{-- Top Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <p class="text-[11px] text-gray-500 font-medium mb-1 uppercase tracking-wider">User Approved</p>
            <h3 class="text-xl font-bold text-emerald-600">{{ format_currency($approvedAmount) }}</h3>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center">
            <p class="text-[11px] text-gray-500 font-medium mb-1 uppercase tracking-wider">User Pending</p>
            <h3 class="text-xl font-bold text-amber-500">{{ format_currency($pendingAmount) }}</h3>
        </div>
    </div>

    {{-- Middle Section --}}
    <div class="grid grid-cols-1 gap-6 mb-6">
        {{-- User Withdrawals --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-6 text-sm">User Withdrawals</h3>
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-xs text-gray-600 font-medium">Approved ({{ $approvedCount }})</span>
                        <span class="text-xs font-bold text-gray-800">{{ format_currency($approvedAmount) }} <span class="text-gray-400 font-normal">({{ round(($approvedAmount / $totalAmount) * 100) }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-gray-200 h-1.5 rounded-full" style="width: {{ ($approvedAmount / $totalAmount) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-xs text-gray-600 font-medium">Pending ({{ $pendingCount }})</span>
                        <span class="text-xs font-bold text-gray-800">{{ format_currency($pendingAmount) }} <span class="text-gray-400 font-normal">({{ round(($pendingAmount / $totalAmount) * 100) }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-gray-200 h-1.5 rounded-full" style="width: {{ ($pendingAmount / $totalAmount) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-xs text-gray-600 font-medium">Rejected ({{ $rejectedCount }})</span>
                        <span class="text-xs font-bold text-gray-800">{{ format_currency($rejectedAmount) }} <span class="text-gray-400 font-normal">({{ round(($rejectedAmount / $totalAmount) * 100) }}%)</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-gray-200 h-1.5 rounded-full" style="width: {{ ($rejectedAmount / $totalAmount) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
