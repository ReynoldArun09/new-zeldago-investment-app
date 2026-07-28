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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Investment</h3>
                <i class="ph ph-chart-line-up text-xl text-primary"></i>
            </div>
            <p class="text-2xl font-bold text-indigo-950 mb-1">{{ format_currency($total_investment) }}</p>
            <p class="text-xs text-primary font-medium flex items-center gap-1">
                <i class="ph ph-trend-up"></i> Invested Amount
            </p>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total ROI</h3>
                <i class="ph ph-chart-polar text-xl text-primary"></i>
            </div>
            <p class="text-2xl font-bold text-indigo-950 mb-1">{{ format_currency($total_roi) }}</p>
            <p class="text-xs text-primary font-medium flex items-center gap-1">
                <i class="ph ph-check-circle"></i> Yields Received
            </p>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Level Bonuses</h3>
                <i class="ph ph-users-three text-xl text-primary"></i>
            </div>
            <p class="text-2xl font-bold text-indigo-950 mb-1">{{ format_currency($total_commissions) }}</p>
            <p class="text-xs text-primary font-medium flex items-center gap-1">
                <i class="ph ph-trend-up"></i> Total Commissions
            </p>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Balance</h3>
                <i class="ph ph-wallet text-xl text-primary"></i>
            </div>
            <p class="text-2xl font-bold text-indigo-950 mb-1">{{ format_currency($wallet_balance) }}</p>
            <p class="text-xs text-primary font-medium flex items-center gap-1">
                <i class="ph ph-wallet"></i> Wallet Available
            </p>
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
                            <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
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
            <div class="space-y-4">
                @foreach($recent_commissions as $comm)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50/50 border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                <i class="ph ph-arrow-down-left text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-indigo-950">{{ $comm->description ?? 'Commission Received' }}</p>
                                <p class="text-xs text-slate-500">{{ $comm->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <p class="text-sm font-bold text-green-600">+{{ format_currency($comm->amount) }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection


