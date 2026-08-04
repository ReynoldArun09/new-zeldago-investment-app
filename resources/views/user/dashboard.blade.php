@php
    $currency = get_setting('currency_symbol') ?? 'Rs.';
@endphp

@extends('user.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div x-data="{ 
    showAddInvestorModal: false, 
    showAddInvestmentModal: false,
    investmentDate: '',
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
            <p class="text-sm text-slate-500">Manage your asset packages, check logs, and monitor yields in real-time.</p>
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
        @endif

        @if(Auth::user()->account_type === 'Agent')
        <!-- Card 3 -->
        <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #00a65a;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Total Commission</p>
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

        <!-- Card 4.5 -->
        <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #00c0ef;">
            <div class="p-4 flex flex-col justify-center">
                <p class="text-xs mb-1 font-medium opacity-90">Total Investments</p>
                <p class="text-xl font-bold tracking-wide">{{ format_currency($network_investments) }}</p>
            </div>
            <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
                <i class="ph ph-briefcase text-2xl opacity-90"></i>
            </div>
        </div>
        @endif
    </div>

    <!-- ROI Returns Area -->
    @if(Auth::user()->account_type !== 'Agent')
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
    <div class="mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm shadow-indigo-100/50 border border-slate-50 flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-indigo-950">My Investments</h3>
                <a href="{{ route('user.investments.active') }}" class="text-xs text-primary font-semibold hover:underline">View All</a>
            </div>
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
                                <th class="px-5 py-3 font-medium">Amount</th>
                                <th class="px-5 py-3 font-medium text-center">Status</th>
                                <th class="px-5 py-3 font-medium text-right">Date</th>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @endif


    <!-- Recent Commissions -->
    @if(Auth::user()->account_type === 'Agent')
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
                                    class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Username <span class="text-red-500">*</span></label>
                                <input type="text" name="username" value="{{ old('username') }}" required
                                    class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" 
                                    class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">City</label>
                                <input type="text" name="city" value="{{ old('city') }}" 
                                    class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                            </div>
                            <div x-data="{ showPassword: false }">
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password" required minlength="8"
                                        class="w-full bg-slate-50/50 border border-slate-200 rounded-sm pl-4 pr-10 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                                        <i class="ph text-lg" :class="showPassword ? 'ph-eye-slash' : 'ph-eye'"></i>
                                    </button>
                                </div>
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
                            <select name="investor_id" required class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                                <option value="">-- Select an Investor --</option>
                                @foreach($agent_investors as $investor)
                                    <option value="{{ $investor->id }}">{{ $investor->name }} ({{ $investor->username }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Amount <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="amount" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all" placeholder="Enter amount">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Investment Date <span class="text-red-500">*</span></label>
                            <input type="date" name="investment_date" x-model="investmentDate" @change="generateMonths()" required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-sm px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
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


