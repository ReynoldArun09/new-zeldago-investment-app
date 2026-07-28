@php
    $currency = get_setting('currency_symbol') ?? 'Rs.';
@endphp

@extends('user.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-indigo-950">Dashboard</h1>
                @if(Auth::user()->kyc_status === 'VERIFIED')
                    <span class="text-[10px] font-bold text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                        <i class="ph ph-check-circle"></i> Verified
                    </span>
                @else
                    <span class="text-[10px] font-bold text-red-500 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                        <i class="ph ph-warning-circle"></i> Not Verified
                    </span>
                @endif
            </div>
            <p class="text-sm text-slate-500">Manage your asset packages, check logs, and monitor yields in real-time.</p>
        </div>
        
        <button class="bg-indigo-100/50 hover:bg-indigo-100 text-primary text-sm font-semibold py-2.5 px-4 rounded-xl transition-colors border border-indigo-100 shadow-sm flex items-center gap-2">
            Download Statements
        </button>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Card 1 -->
        <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #00c0ef;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Total Investment</p>
                <p class="text-xl font-bold tracking-wide">{{ format_currency($total_investment) }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
                <i class="ph ph-briefcase text-2xl opacity-90"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #605ca8;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Total ROI</p>
                <p class="text-xl font-bold tracking-wide">{{ format_currency($total_roi) }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
                <i class="ph ph-chart-line-up text-2xl opacity-90"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #00a65a;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Level Bonuses</p>
                <p class="text-xl font-bold tracking-wide">{{ format_currency($total_commissions) }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
                <i class="ph ph-users-three text-2xl opacity-90"></i>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #f39c12;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Total Balance</p>
                <p class="text-xl font-bold tracking-wide">{{ format_currency($wallet_balance) }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
                <i class="ph ph-wallet text-2xl opacity-90"></i>
            </div>
        </div>
    </div>

    <!-- ROI Returns Area -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50 flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-indigo-950">Recent ROI Returns</h3>
            </div>
            @if($recent_rois->isEmpty())
                <div class="flex-1 flex items-center justify-center py-10">
                    <p class="text-sm text-slate-400">No recent ROI returns</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                                <th class="px-5 py-3 font-medium">Investment</th>
                                <th class="px-5 py-3 font-medium">Date</th>
                                <th class="px-5 py-3 font-medium text-right">Amount</th>
                                <th class="px-5 py-3 font-medium text-center">Rate</th>
                                <th class="px-5 py-3 font-medium text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($recent_rois as $roi)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 py-4">
                                        <p class="text-sm font-bold text-indigo-950">
                                            {{ $roi->investment->plan->name ?? 'Investment' }}
                                        </p>
                                        <p class="text-xs text-slate-500">{{ $roi->investment->trx_id ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="text-sm text-slate-600">{{ $roi->created_at->format('M d, Y') }}</p>
                                        <p class="text-xs text-slate-400">{{ $roi->created_at->format('h:i A') }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <p class="text-sm font-bold text-green-600">+{{ format_currency($roi->amount) }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <p class="text-sm font-medium text-slate-600">{{ $roi->rate }}%</p>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="px-2 py-1 rounded text-xs font-medium 
                                            @if($roi->status == 'approved') bg-green-100 text-green-700
                                            @elseif($roi->status == 'pending') bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ ucfirst($roi->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Commissions -->
    <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50 flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-indigo-950">Recent Commissions</h3>
            <a href="{{ route('user.finance.transactions') }}" class="text-xs text-primary font-semibold hover:underline">View All</a>
        </div>
        @if($recent_commissions->isEmpty())
            <div class="flex-1 flex items-center justify-center py-10">
                <p class="text-sm text-slate-400">No recent commissions</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                            <th class="px-5 py-3 font-medium">Description</th>
                            <th class="px-5 py-3 font-medium">Date</th>
                            <th class="px-5 py-3 font-medium text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recent_commissions as $comm)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                            <i class="ph ph-arrow-down-left text-lg"></i>
                                        </div>
                                        <p class="text-sm font-bold text-indigo-950">{{ $comm->description ?? 'Commission Received' }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-sm text-slate-600">{{ $comm->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-slate-400">{{ $comm->created_at->format('h:i A') }}</p>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <p class="text-sm font-bold text-green-600">+{{ format_currency($comm->amount) }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection


