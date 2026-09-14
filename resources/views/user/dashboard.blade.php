@php
    $currency = get_setting('currency_symbol') ?? 'Rs.';
@endphp

@extends('user.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div x-data="{ 
    showAddInvestorModal: {{ old('username') !== null && $errors->any() ? 'true' : 'false' }}, 
    showAddInvestmentModal: {{ old('investor_id') !== null && old('investment_id') === null && $errors->any() ? 'true' : 'false' }},

    investmentDate: '{{ old('investment_date') }}',
    roiMonths: [],
    generateMonths() {
        this.roiMonths = [];
        if (!this.investmentDate) return;
        
        let startDate = new Date(this.investmentDate);
        if (isNaN(startDate.getTime())) return;
        
        let now = new Date();
        let targetMonth = now.getMonth() - 1;
        let targetYear = now.getFullYear();
        if (targetMonth < 0) {
            targetMonth = 11;
            targetYear--;
        }
        
        let currentMonth = startDate.getMonth();
        let currentYear = startDate.getFullYear();
        
        while (currentYear < targetYear || (currentYear === targetYear && currentMonth <= targetMonth)) {
            let lastDay = new Date(currentYear, currentMonth + 1, 0);
            this.roiMonths.push({
                dateStr: lastDay.getFullYear() + '-' + String(lastDay.getMonth() + 1).padStart(2, '0') + '-' + String(lastDay.getDate()).padStart(2, '0'),
                displayStr: String(lastDay.getDate()).padStart(2, '0') + '/' + String(lastDay.getMonth() + 1).padStart(2, '0') + '/' + lastDay.getFullYear(),
                amount: ''
            });
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
        }
    }
}">
    @if(Auth::user()->contract_date && Auth::user()->contract_notify_date && \Carbon\Carbon::now()->startOfDay()->gte(\Carbon\Carbon::parse(Auth::user()->contract_notify_date)->startOfDay()))
        <div class="mb-6 py-4 px-3 rounded-none bg-red-600 border border-red-700 text-white flex items-center">
            <i class="ph ph-warning-circle text-xl text-white mr-3"></i>
            <marquee class="font-medium text-sm flex-1">
                <strong>Contract Reminder:</strong> 
                @if(Auth::user()->contract_message)
                    {{ Auth::user()->contract_message }}
                @else
                    Your contract is ending on {{ \Carbon\Carbon::parse(Auth::user()->contract_date)->format('d M Y') }}.
                @endif
            </marquee>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-none relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-none relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div>
            <p class="text-4xl font-bold text-slate-700 mb-2">Welcome, <span class="text-indigo-600">{{ Auth::user()->name }}</span>!</p>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl font-bold text-indigo-950">{{ Auth::user()->account_type === 'Agent' ? 'Agent' : 'Investor' }} Dashboard</h1>
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
            <p class="text-sm text-slate-500">manage your investmemts, roi returns</p>
        </div>
        
        <div class="flex items-center gap-2">
            @if(Auth::user()->account_type === 'Agent')
            <button @click="showAddInvestorModal = true" class="bg-primary hover:opacity-90 text-white text-sm font-semibold py-2.5 px-4 rounded-none transition-colors border border-primary shadow-sm flex items-center gap-2">
                <i class="ph ph-user-plus"></i> Add Investor
            </button>
            <button @click="showAddInvestmentModal = true" class="bg-primary hover:opacity-90 text-white text-sm font-semibold py-2.5 px-4 rounded-none transition-colors border border-primary shadow-sm flex items-center gap-2">
                <i class="ph ph-plus-circle"></i> Add Investments
            </button>

            @endif
            <a href="{{ route('user.statements.download') }}" class="bg-indigo-100/50 hover:bg-indigo-100 text-primary text-sm font-semibold py-2.5 px-4 rounded-none transition-colors border border-indigo-100 shadow-sm flex items-center gap-2">
                Download Statements
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @if(Auth::user()->account_type !== 'Agent')
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

        <!-- Card 5 (Active Investments) -->
        <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #00a65a;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Active Investments</p>
                <p class="text-xl font-bold tracking-wide">{{ $active_investments_count }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
                <i class="ph ph-activity text-2xl opacity-90"></i>
            </div>
        </div>

        <!-- Card 6 (Closed Investments) -->
        <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #dd4b39;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Closed Investments</p>
                <p class="text-xl font-bold tracking-wide">{{ $closed_investments_count }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
                <i class="ph ph-archive-box text-2xl opacity-90"></i>
            </div>
        </div>

        <!-- Card 7 (Old Investments) -->
        <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #f39c12;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Old Investments</p>
                <p class="text-xl font-bold tracking-wide">{{ format_currency($old_investments_amount) }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
                <i class="ph ph-clock-counter-clockwise text-2xl opacity-90"></i>
            </div>
        </div>
        
        <!-- Card 8 (Principal withdrawal) -->
        <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #9c27b0;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Principal withdrawal</p>
                <p class="text-xl font-bold tracking-wide">{{ format_currency($total_direct_roi) }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
                <i class="ph ph-hand-coins text-2xl opacity-90"></i>
            </div>
        </div>
        @endif

        @if(Auth::user()->account_type === 'Agent')
        <!-- Card 3 -->
        <div class="flex justify-between shadow-sm h-24 rounded-sm overflow-hidden bg-white" style="border: 2px solid #00a65a; color: #00a65a;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Total Commission</p>
                <p class="text-xl font-bold tracking-wide">{{ format_currency($total_commissions) }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0, 166, 90, 0.1);">
                <i class="ph ph-users-three text-2xl opacity-90"></i>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="flex justify-between shadow-sm h-24 rounded-sm overflow-hidden bg-white" style="border: 2px solid #f39c12; color: #f39c12;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Total Balance</p>
                <p class="text-xl font-bold tracking-wide">{{ format_currency($wallet_balance) }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(243, 156, 18, 0.1);">
                <i class="ph ph-wallet text-2xl opacity-90"></i>
            </div>
        </div>

        <!-- Card 4.5 -->
        <div class="flex justify-between shadow-sm h-24 rounded-sm overflow-hidden bg-white" style="border: 2px solid #00c0ef; color: #00c0ef;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Total Investments</p>
                <p class="text-xl font-bold tracking-wide">{{ format_currency($network_investments) }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0, 192, 239, 0.1);">
                <i class="ph ph-briefcase text-2xl opacity-90"></i>
            </div>
        </div>
        @endif
    </div>

    <!-- ROI Returns Area -->
    @if(Auth::user()->account_type !== 'Agent')
    <div class="mb-8" x-data="{ tab: 'recent' }">
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50 flex flex-col">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h3 class="text-sm font-bold text-indigo-950" x-text="tab === 'recent' ? 'Recent ROI Returns' : 'Principal withdrawal'">Recent ROI Returns</h3>
                
                <div class="flex bg-gray-100 p-1 rounded-lg">
                    <button @click="tab = 'recent'" :class="tab === 'recent' ? 'shadow-sm text-white' : 'text-gray-500 hover:text-gray-700'" :style="tab === 'recent' ? 'background-color: var(--primary);' : ''" class="px-4 py-1.5 text-xs font-medium rounded-md transition-all">Recent ROI Returns</button>
                    <button @click="tab = 'direct'" :class="tab === 'direct' ? 'shadow-sm text-white' : 'text-gray-500 hover:text-gray-700'" :style="tab === 'direct' ? 'background-color: var(--primary);' : ''" class="px-4 py-1.5 text-xs font-medium rounded-md transition-all">Principal withdrawal</button>
                </div>
            </div>

            <!-- Recent ROI Returns Tab -->
            <div x-show="tab === 'recent'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                @if($recent_rois->isEmpty())
                    <div class="flex-1 flex items-center justify-center py-10">
                        <p class="text-sm text-slate-400">No recent ROI returns</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                                    <th class="px-5 py-3 font-medium">Investment ID</th>
                                    <th class="px-5 py-3 font-medium">Investment Amount</th>
                                    <th class="px-5 py-3 font-medium">Date</th>
                                    <th class="px-5 py-3 font-medium text-right">ROI Amount</th>
                                    <th class="px-5 py-3 font-medium text-center">ROI Rate</th>
                                    <th class="px-5 py-3 font-medium text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($recent_rois as $roi)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-bold text-indigo-950">
                                                Investment
                                            </p>
                                            <p class="text-xs text-slate-500">{{ $roi->investment->trx_id ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-bold text-slate-600">{{ format_currency($roi->investment->amount ?? 0) }}</p>
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
                                                @if($roi->status == 'credited' || $roi->status == 'approved') bg-green-100 text-green-700
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

            <!-- Principal withdrawal Tab -->
            <div x-show="tab === 'direct'" class="p-0 overflow-x-auto" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                @if($recent_direct_rois->isEmpty())
                    <div class="flex-1 flex items-center justify-center py-10">
                        <p class="text-sm text-slate-400">No principal withdrawals yet</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                                    <th class="px-5 py-3 font-medium">Investment ID</th>
                                    <th class="px-5 py-3 font-medium">Investment Amount</th>
                                    <th class="px-5 py-3 font-medium">Date</th>
                                    <th class="px-5 py-3 font-medium text-right">Direct Amount</th>
                                    <th class="px-5 py-3 font-medium text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($recent_direct_rois as $roi)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-bold text-indigo-950">
                                                Old Investment
                                            </p>
                                            <p class="text-xs text-slate-500">{{ $roi->investment->trx_id ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-bold text-slate-600">{{ format_currency($roi->investment->amount ?? 0) }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm text-slate-600">{{ $roi->created_at->format('M d, Y') }}</p>
                                            <p class="text-xs text-slate-400">{{ $roi->created_at->format('h:i A') }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <p class="text-sm font-bold text-purple-600">+{{ format_currency($roi->direct_roi_amount) }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <span class="px-2 py-1 rounded text-xs font-medium 
                                                @if($roi->status == 'credited' || $roi->status == 'approved') bg-green-100 text-green-700
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
    </div>
    <div class="mb-8" x-data="{ invTab: 'normal' }">
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50 flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-4">
                    <h3 class="text-sm font-bold text-indigo-950" x-text="invTab === 'normal' ? 'My Investments' : 'My Old Investments'">My Investments</h3>
                    <div class="flex bg-gray-100 p-1 rounded-lg">
                        <button @click="invTab = 'normal'" :class="invTab === 'normal' ? 'shadow-sm text-white' : 'text-gray-500 hover:text-gray-700'" :style="invTab === 'normal' ? 'background-color: var(--primary);' : ''" class="px-4 py-1.5 text-xs font-medium rounded-md transition-all">My Investments</button>
                        <button @click="invTab = 'old'" :class="invTab === 'old' ? 'shadow-sm text-white' : 'text-gray-500 hover:text-gray-700'" :style="invTab === 'old' ? 'background-color: var(--primary);' : ''" class="px-4 py-1.5 text-xs font-medium rounded-md transition-all">My Old Investments</button>
                    </div>
                </div>
                <a href="{{ route('user.investments.active') }}" class="text-xs text-primary font-semibold hover:underline">View All</a>
            </div>

            <!-- Normal Investments Tab -->
            <div x-show="invTab === 'normal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                @if($user_investments->isEmpty())
                    <div class="flex-1 flex items-center justify-center py-10">
                        <p class="text-sm text-slate-400">No active investments</p>
                    </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                                <th class="px-5 py-3 font-medium">Transaction ID</th>
                                <th class="px-5 py-3 font-medium">Investment Amount</th>
                                <th class="px-5 py-3 font-medium">Date</th>
                                <th class="px-5 py-3 font-medium text-center">Status</th>
                                <th class="px-5 py-3 font-medium text-right">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($user_investments as $inv)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 py-4">
                                        <p class="text-sm font-bold text-indigo-950">
                                            {{ $inv->trx_id ?? 'N/A' }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="text-sm font-bold text-green-600">{{ format_currency($inv->amount) }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="text-sm text-slate-600">{{ $inv->created_at->format('M d, Y') }}</p>
                                        <p class="text-xs text-slate-400">{{ $inv->created_at->format('h:i A') }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-center">
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
                                    <td class="px-5 py-4 text-right" x-data="{ showRoiModal: false }">
                                        <button @click="showRoiModal = true" class="text-xs font-semibold text-primary hover:underline bg-indigo-50 px-3 py-1.5 rounded-md border border-indigo-100 transition-colors hover:bg-indigo-100">View ROI</button>
                                        
                                        <!-- ROI Modal -->
                                        <template x-teleport="body">
                                            <div x-show="showRoiModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                                <div x-show="showRoiModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showRoiModal = false"></div>
                                                
                                                <div x-show="showRoiModal" 
                                                    x-transition:enter="ease-out duration-300"
                                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave="ease-in duration-200"
                                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    class="relative bg-white rounded-2xl shadow-xl w-[90%] max-w-2xl max-h-[90vh] overflow-hidden flex flex-col z-10 border border-indigo-50 text-left">
                                                    
                                                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
                                                        <h3 class="font-bold text-lg text-indigo-950 flex items-center gap-2">
                                                            <i class="ph ph-chart-line-up text-primary"></i> ROI Returns - {{ $inv->trx_id ?? 'N/A' }}
                                                        </h3>
                                                        <button @click="showRoiModal = false" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100">
                                                            <i class="ph ph-x text-lg"></i>
                                                        </button>
                                                    </div>

                                                    <div class="p-6 overflow-y-auto">
                                                        @if($inv->roiLogs->isEmpty())
                                                            <div class="flex flex-col items-center justify-center py-10">
                                                                <i class="ph ph-chart-line-down text-4xl text-slate-300 mb-3"></i>
                                                                <p class="text-sm text-slate-400">No ROI returns found for this investment.</p>
                                                            </div>
                                                        @else
                                                            <div class="overflow-x-auto">
                                                                <table class="w-full text-left border-collapse">
                                                                    <thead>
                                                                        <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                                                                            <th class="px-5 py-3 font-medium">Date</th>
                                                                            <th class="px-5 py-3 font-medium text-right">Amount</th>
                                                                            <th class="px-5 py-3 font-medium text-center">Rate</th>
                                                                            <th class="px-5 py-3 font-medium text-center">Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="divide-y divide-slate-50">
                                                                        @foreach($inv->roiLogs as $roi)
                                                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                                                <td class="px-5 py-4">
                                                                                    <p class="text-sm text-slate-600">{{ $roi->created_at->format('M d, Y') }}</p>
                                                                                    <p class="text-xs text-slate-400">{{ $roi->created_at->format('h:i A') }}</p>
                                                                                </td>
                                                                                <td class="px-5 py-4 text-right">
                                                                                    <p class="text-sm font-bold text-green-600">+{{ format_currency($roi->amount) }}</p>
                                                                                </td>
                                                                                <td class="px-5 py-4 text-center">
                                                                                    <p class="text-sm font-medium text-slate-600">{{ $roi->rate ?? '-' }}%</p>
                                                                                </td>
                                                                                <td class="px-5 py-4 text-center">
                                                                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                                                                        @if($roi->status == 'credited' || $roi->status == 'approved') bg-green-100 text-green-700
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
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            </div>

            <!-- Old Investments Tab -->
            <div x-show="invTab === 'old'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                @if($old_user_investments->isEmpty())
                    <div class="flex-1 flex items-center justify-center py-10">
                        <p class="text-sm text-slate-400">No old investments</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                                    <th class="px-5 py-3 font-medium">Transaction ID</th>
                                    <th class="px-5 py-3 font-medium">Investment Amount</th>
                                    <th class="px-5 py-3 font-medium">Date</th>
                                    <th class="px-5 py-3 font-medium text-center">Status</th>
                                    <th class="px-5 py-3 font-medium text-right">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($old_user_investments as $inv)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-bold text-indigo-950">
                                                {{ $inv->trx_id ?? 'N/A' }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-bold text-green-600">{{ format_currency($inv->amount) }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm text-slate-600">{{ $inv->created_at->format('M d, Y') }}</p>
                                            <p class="text-xs text-slate-400">{{ $inv->created_at->format('h:i A') }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-center">
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
                                        <td class="px-5 py-4 text-right" x-data="{ showRoiModal: false }">
                                            <button @click="showRoiModal = true" class="text-xs font-semibold text-primary hover:underline bg-indigo-50 px-3 py-1.5 rounded-md border border-indigo-100 transition-colors hover:bg-indigo-100">View ROI</button>
                                            
                                            <!-- ROI Modal -->
                                            <template x-teleport="body">
                                                <div x-show="showRoiModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                                    <div x-show="showRoiModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showRoiModal = false"></div>
                                                    
                                                    <div x-show="showRoiModal" 
                                                        x-transition:enter="ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave="ease-in duration-200"
                                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        class="relative bg-white rounded-2xl shadow-xl w-[90%] max-w-2xl max-h-[90vh] overflow-hidden flex flex-col z-10 border border-indigo-50 text-left">
                                                        
                                                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
                                                            <h3 class="font-bold text-lg text-indigo-950 flex items-center gap-2">
                                                                <i class="ph ph-chart-line-up text-primary"></i> ROI Returns - {{ $inv->trx_id ?? 'N/A' }}
                                                            </h3>
                                                            <button @click="showRoiModal = false" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100">
                                                                <i class="ph ph-x text-lg"></i>
                                                            </button>
                                                        </div>
                                                        
                                                        <div class="p-6 overflow-y-auto bg-slate-50/30 flex-1">
                                                            @if($inv->roiLogs->isEmpty())
                                                                <div class="text-center py-8">
                                                                    <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                                                        <i class="ph ph-chart-line-down text-xl text-indigo-400"></i>
                                                                    </div>
                                                                    <p class="text-slate-500 font-medium text-sm">No ROI returns yet.</p>
                                                                    <p class="text-slate-400 text-xs mt-1">Returns will appear here once credited.</p>
                                                                </div>
                                                            @else
                                                                <div class="space-y-3">
                                                                    @foreach($inv->roiLogs()->orderBy('created_at', 'desc')->get() as $log)
                                                                        <div class="bg-white border border-slate-100 p-4 rounded-xl shadow-sm hover:shadow-md transition-shadow flex justify-between items-center group">
                                                                            <div class="flex items-center gap-4">
                                                                                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                                                                    <i class="ph ph-money text-green-600 text-lg"></i>
                                                                                </div>
                                                                                <div>
                                                                                    <p class="text-sm font-bold text-slate-700">Return #{{ $loop->iteration }}</p>
                                                                                    <p class="text-xs text-slate-500">{{ $log->created_at->format('M d, Y') }} &bull; {{ $log->created_at->format('h:i A') }}</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="text-right">
                                                                                <p class="font-bold text-green-600">+{{ format_currency($log->amount > 0 ? $log->amount : $log->direct_roi_amount) }}</p>
                                                                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-medium 
                                                                                    @if($log->status == 'credited') bg-green-100 text-green-700
                                                                                    @elseif($log->status == 'pending') bg-yellow-100 text-yellow-700
                                                                                    @else bg-red-100 text-red-700
                                                                                    @endif">
                                                                                    {{ ucfirst($log->status) }}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif


    <!-- Agent Investors Investments -->
    @if(Auth::user()->account_type === 'Agent')
    <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50 flex flex-col mb-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-indigo-950">My Investor Investments</h3>
            <a href="{{ route('user.network.referrals') }}" class="text-xs text-primary font-semibold hover:underline">View All</a>
        </div>
        @if($agent_investor_investments->isEmpty())
            <div class="flex-1 flex items-center justify-center py-10">
                <p class="text-sm text-slate-400">No recent investments from investors</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                            <th class="px-5 py-3 font-medium">Investor</th>
                            <th class="px-5 py-3 font-medium">Transaction ID</th>
                            <th class="px-5 py-3 font-medium text-center">Status</th>
                            <th class="px-5 py-3 font-medium text-right">Date</th>
                            <th class="px-5 py-3 font-medium text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($agent_investor_investments as $inv)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="text-sm font-bold text-indigo-950">{{ $inv->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-slate-500">{{ $inv->user->username ?? 'N/A' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-bold text-indigo-950">{{ $inv->trx_id ?? 'N/A' }}</p>
                                </td>
                                <td class="px-5 py-4 text-center">
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
                                <td class="px-5 py-4 text-right">
                                    <p class="text-sm text-slate-600">{{ $inv->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-slate-400">{{ $inv->created_at->format('h:i A') }}</p>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <p class="text-sm font-bold text-green-600">{{ format_currency($inv->amount) }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Recent Commissions -->
    <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50 flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-indigo-950">Recent Commissions</h3>
            <a href="{{ route('user.finance.transactions.commissions') }}" class="text-xs text-primary font-semibold hover:underline">View All</a>
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

    <!-- Recent Withdrawals -->
    <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50 flex flex-col mt-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-indigo-950">Recent Withdrawals</h3>
            <a href="{{ route('user.finance.withdrawals') }}" class="text-xs text-primary font-semibold hover:underline">View All</a>
        </div>
        @if($recent_withdrawals->isEmpty())
            <div class="flex-1 flex items-center justify-center py-10">
                <p class="text-sm text-slate-400">No recent withdrawals</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                            <th class="px-5 py-3 font-medium">Method</th>
                            <th class="px-5 py-3 font-medium">Date</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recent_withdrawals as $withdrawal)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                            <i class="ph ph-arrow-up-right text-lg"></i>
                                        </div>
                                        <p class="text-sm font-bold text-indigo-950">{{ $withdrawal->bank_name ?? 'Bank Transfer' }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-sm text-slate-600">{{ $withdrawal->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-slate-400">{{ $withdrawal->created_at->format('h:i A') }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    @if($withdrawal->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase tracking-wider">
                                            Approved
                                        </span>
                                    @elseif($withdrawal->status === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 uppercase tracking-wider">
                                            Rejected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 uppercase tracking-wider">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <p class="text-sm font-bold text-red-600">-{{ format_currency($withdrawal->amount) }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endif

    @if(Auth::user()->account_type === 'Agent')
    <!-- Add Investor Modal -->
    <template x-teleport="body">
        <div x-show="showAddInvestorModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="showAddInvestorModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showAddInvestorModal = false"></div>
            
            <div x-show="showAddInvestorModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-2xl shadow-xl w-[40%] max-h-[90vh] overflow-hidden flex flex-col z-10 border border-indigo-50">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
                    <h3 class="font-bold text-lg text-indigo-950 flex items-center gap-2">
                        <i class="ph ph-user-plus text-primary"></i> Add Investor
                    </h3>
                    <button @click="showAddInvestorModal = false" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100">
                        <i class="ph ph-x text-lg"></i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form method="POST" action="{{ route('user.network.add-investor') }}" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <h2 class="text-sm font-semibold text-slate-800 border-b border-slate-100 pb-2 mb-4">Information</h2>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Account Type</label>
                                <p class="text-sm font-medium text-slate-800">Investor</p>
                                <input type="hidden" name="account_type" value="Normal User">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Sponsor</label>
                                <p class="text-sm font-medium text-slate-800">{{ Auth::user()->username }}</p>
                            </div>

                            <div class="sm:col-span-2 mt-2">
                                <h2 class="text-sm font-semibold text-slate-800 border-b border-slate-100 pb-2 mb-4">Basic Information</h2>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('name') border-red-500 @enderror">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Username <span class="text-red-500">*</span></label>
                                <input type="text" name="username" value="{{ old('username') }}" required
                                    class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('username') border-red-500 @enderror">
                                @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('email') border-red-500 @enderror">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" 
                                    class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('phone') border-red-500 @enderror">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">City</label>
                                <input type="text" name="city" value="{{ old('city') }}" 
                                    class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('city') border-red-500 @enderror">
                                @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div x-data="{ showPassword: false }">
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password" required minlength="8"
                                        class="w-full bg-slate-50/50 border border-slate-200 rounded-sm pl-4 pr-10 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('password') border-red-500 @enderror">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                        <i class="ph text-lg" :class="showPassword ? 'ph-eye-slash' : 'ph-eye'"></i>
                                    </button>
                                </div>
                                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-8 pt-5 border-t border-slate-100 flex justify-end gap-3 shrink-0">
                            <button type="button" @click="showAddInvestorModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-sm transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-primary hover:opacity-90 rounded-sm transition-opacity flex items-center gap-2">
                                Add Investor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
    <!-- Add Investment Modal -->
    <template x-teleport="body">
        <div x-show="showAddInvestmentModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="showAddInvestmentModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showAddInvestmentModal = false"></div>
            
            <div x-show="showAddInvestmentModal" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-2xl shadow-xl w-[40%] max-h-[90vh] overflow-hidden flex flex-col z-10 border border-indigo-50">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
                    <h3 class="font-bold text-lg text-indigo-950 flex items-center gap-2">
                        <i class="ph ph-briefcase text-primary"></i> Add Investment
                    </h3>
                    <button @click="showAddInvestmentModal = false" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100">
                        <i class="ph ph-x text-lg"></i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form method="POST" action="{{ route('user.network.add-investment') }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Select Investor <span class="text-red-500">*</span></label>
                            <select name="investor_id" required class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('investor_id') border-red-500 @enderror">
                                <option value="">-- Select an Investor --</option>
                                @foreach($agent_investors as $investor)
                                    <option value="{{ $investor->id }}" {{ old('investor_id') == $investor->id ? 'selected' : '' }}>{{ $investor->name }} ({{ $investor->username }})</option>
                                @endforeach
                            </select>
                            @error('investor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Amount <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('amount') border-red-500 @enderror" placeholder="Enter amount">
                            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Investment Date <span class="text-red-500">*</span></label>
                            <input type="date" name="investment_date" x-model="investmentDate" @change="generateMonths()" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all @error('investment_date') border-red-500 @enderror">
                            @error('investment_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mt-4" x-show="roiMonths.length > 0">
                            <h4 class="text-sm font-semibold text-slate-800 mb-3 border-b border-slate-100 pb-2">ROI Payouts (Till Last Month)</h4>
                            
                            <template x-for="(month, index) in roiMonths" :key="index">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="flex-1">
                                        <input type="date" :name="`roi_dates[${index}]`" x-model="month.dateStr" required
                                            class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-3 py-2 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                                    </div>
                                    <div class="flex-1">
                                        <input type="number" step="0.01" :name="`roi_amounts[${index}]`" x-model="month.amount" placeholder="ROI Payout"
                                            class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-3 py-2 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                                    </div>
                                    <button type="button" @click="roiMonths.splice(index, 1)" class="text-red-500 hover:text-red-700 p-2 transition-colors">
                                        <i class="ph ph-trash text-lg"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="mt-8 pt-5 border-t border-slate-100 flex justify-end gap-3 shrink-0">
                            <button type="button" @click="showAddInvestmentModal = false" class="px-5 py-2.5 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-sm transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-primary hover:opacity-90 rounded-sm transition-opacity flex items-center gap-2">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    @endif
</div>
    
    <!-- Alpine.js is already included in app.blade.php -->
@endsection


