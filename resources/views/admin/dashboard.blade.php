@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-full p-4 sm:p-6 space-y-6">

    <h1 class="text-lg font-semibold text-gray-700">Admin Dashboard</h1>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Investments --}}
        <div class="bg-[#9333ea] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] opacity-90 truncate mb-1">Total Investments</p>
                <p class="text-xl font-bold truncate">{{ format_currency($stats['totalBusiness']) }}</p>
            </div>
            <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>

        {{-- Pending Investments --}}
        <div class="bg-[#f59e0b] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] opacity-90 truncate mb-1">Pending Investments</p>
                <p class="text-xl font-bold truncate">{{ format_currency($stats['totalPendingBusiness']) }}</p>
            </div>
            <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        {{-- Total Investors --}}
        <div class="bg-[#0ea5e9] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] opacity-90 truncate mb-1">Total Investors</p>
                <p class="text-xl font-bold truncate">{{ $stats['totalInvestors'] }}</p>
            </div>
            <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        {{-- Total Agents --}}
        <div class="bg-[#4f46e5] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] opacity-90 truncate mb-1">Total Agents</p>
                <p class="text-xl font-bold truncate">{{ $stats['totalAgents'] }}</p>
            </div>
            <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
        </div>

        {{-- Active Members --}}
        <div class="bg-[#dc2626] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] opacity-90 truncate mb-1">Active Members</p>
                <p class="text-xl font-bold truncate">{{ $stats['activeMembers'] }}</p>
            </div>
            <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>

        {{-- New Registrations --}}
        <div class="bg-[#10b981] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] opacity-90 truncate mb-1">New Registrations (7d)</p>
                <p class="text-xl font-bold truncate">{{ $stats['newRegistrations'] }}</p>
            </div>
            <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
        </div>

        {{-- Total Income Paid --}}
        <div class="bg-[#0d9488] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
            <div class="min-w-0 flex-1">
                <p class="text-[11px] opacity-90 truncate mb-1">Total Income Paid</p>
                <p class="text-xl font-bold truncate">{{ format_currency($stats['totalIncomePaid']) }}</p>
            </div>
            <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

    </div>

    {{-- Withdrawals Cards --}}
    <div class="mt-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Withdrawals</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- Total Withdrawn --}}
            <div class="bg-[#0d9488] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] opacity-90 truncate mb-1">Total Withdrawn</p>
                    <p class="text-xl font-bold truncate">{{ format_currency($stats['totalWithdrawn']) }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>

            {{-- Pending Withdrawals --}}
            <div class="bg-[#f59e0b] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] opacity-90 truncate mb-1">Pending Withdrawals</p>
                    <p class="text-xl font-bold truncate">{{ $stats['pendingWithdrawals'] }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

            {{-- Rejected Withdrawals --}}
            <div class="bg-[#ef4444] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] opacity-90 truncate mb-1">Rejected Withdrawals</p>
                    <p class="text-xl font-bold truncate">{{ $stats['rejectedWithdrawals'] }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Withdrawal Charge --}}
            <div class="bg-[#9333ea] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] opacity-90 truncate mb-1">Withdrawal Charge</p>
                    <p class="text-xl font-bold truncate">{{ format_currency($stats['withdrawalCharge']) }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Payouts Cards --}}
    <div class="mt-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Payouts</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- Total ROI Paid --}}
            <div class="bg-[#10b981] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] opacity-90 truncate mb-1">Total ROI Paid</p>
                    <p class="text-xl font-bold truncate">{{ format_currency($stats['totalRoiPaid']) }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Total Commission Paid --}}
            <div class="bg-[#3b82f6] rounded-none p-4 flex items-center justify-between shadow-sm text-white">
                <div class="min-w-0 flex-1">
                    <p class="text-[11px] opacity-90 truncate mb-1">Total Commission Paid</p>
                    <p class="text-xl font-bold truncate">{{ format_currency($stats['totalCommissionPaid']) }}</p>
                </div>
                <div class="w-10 h-10 rounded bg-white/20 flex items-center justify-center shrink-0 ml-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>

        </div>
    </div>

    {{-- Dashboard Tables Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">

        {{-- Pending ROI Requests --}}
        <div class="bg-white rounded-none shadow-sm flex flex-col xl:col-span-2">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" style="color: var(--theme-primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Pending ROI Requests</h2>
                </div>
                <a href="{{ route('admin.roi.pending') }}" class="text-xs text-indigo-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-white" style="background-color: var(--theme-primary);">
                            <th class="text-left px-5 py-3 font-medium">User</th>
                            <th class="text-left px-5 py-3 font-medium">Contact Info</th>
                            <th class="text-left px-5 py-3 font-medium">Investment</th>
                            <th class="text-right px-5 py-3 font-medium">Amount</th>
                            <th class="text-right px-5 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($pendingRois as $roi)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $roi->user->name ?? 'N/A' }}</p>
                                <p class="text-xs" style="color: var(--theme-primary);">{{ '@' . ($roi->user->username ?? '') }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-sm text-gray-600">{{ $roi->user->email ?? 'N/A' }}</p>
                                @if(!empty($roi->user->phone))
                                    <p class="text-xs text-gray-500">{{ $roi->user->phone }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-sm font-medium text-gray-700">#{{ $roi->investment_id ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $roi->created_at->format('M d, Y h:i A') }}</p>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-emerald-600">
                                {{ format_currency($roi->amount) }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-2" x-data="{ openReject: false }">
                                    <form action="{{ route('admin.roi.process', $roi->id) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 text-white bg-blue-500 hover:bg-blue-600 rounded text-[11px] font-medium transition-colors">
                                            Processing
                                        </button>
                                    </form>
                                    <button type="button" @click="openReject = true" class="px-3 py-1 text-white bg-[#E2000F] hover:bg-red-700 rounded text-[11px] font-medium transition-colors">
                                        Reject
                                    </button>

                                    <!-- Reject Modal -->
                                    <div x-show="openReject" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div x-show="openReject" @click="openReject = false" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                <div class="absolute inset-0 bg-gray-900/75 backdrop-blur-sm"></div>
                                            </div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                            <div x-show="openReject" x-transition.scale.origin.bottom class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
                                                <form action="{{ route('admin.roi.reject', $roi->id) }}" method="POST">
                                                    @csrf
                                                    <div class="bg-white px-6 pt-5 pb-4">
                                                        <h3 class="text-xl font-bold text-gray-800 mb-4 text-left" id="modal-title-reject">
                                                            Reject ROI Request
                                                        </h3>
                                                        <hr class="border-gray-100 mb-4 -mx-6">
                                                        <div class="space-y-4 text-left">
                                                            <div>
                                                                <label class="block text-sm text-gray-600 mb-1">Rejection Note</label>
                                                                <textarea name="reject_note" required rows="3" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm p-3 outline-none" placeholder="Explain why this request is being rejected..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="bg-white px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                                                        <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-[#E2000F] text-sm font-medium text-white hover:bg-red-700 focus:outline-none transition-opacity">
                                                            Reject Request
                                                        </button>
                                                        <button type="button" @click="openReject = false" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors">
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
                            <td colspan="5" class="px-5 py-8 text-center text-xs text-gray-400">No pending ROI requests</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Investments --}}
        <div class="bg-white rounded-none shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Recent Investments</h2>
                </div>
                <a href="{{ route('admin.investments.index') }}" class="text-xs text-indigo-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-white" style="background-color: var(--theme-primary);">
                            <th class="text-left px-5 py-3 font-medium">User</th>
                            <th class="text-left px-5 py-3 font-medium">Trx ID</th>
                            <th class="text-right px-5 py-3 font-medium">Amount</th>
                            <th class="text-center px-5 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentInvestments as $inv)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $inv->user->name ?? 'N/A' }}</p>
                                @if(!empty($inv->user->username))
                                    <p class="text-xs" style="color: var(--theme-primary);">{{ '@' . $inv->user->username }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-gray-600">
                                {{ $inv->trx_id ?? 'N/A' }}
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-indigo-600">
                                {{ format_currency($inv->amount) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @php
                                    $statusClass = match($inv->status) {
                                        'COMPLETED' => 'bg-emerald-100 text-emerald-700',
                                        'ACTIVE'    => 'bg-blue-100 text-blue-700',
                                        'PENDING'   => 'bg-amber-100 text-amber-700',
                                        'REJECTED'  => 'bg-red-100 text-red-700',
                                        default     => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="px-2 py-1 text-[10px] uppercase font-bold rounded-full {{ $statusClass }}">
                                    {{ $inv->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-xs text-gray-400">No recent investments</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pending KYCs --}}
        <div class="bg-white rounded-none shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Pending KYCs</h2>
                </div>
                <a href="{{ route('admin.verification.kyc', ['status' => 'pending']) }}" class="text-xs text-indigo-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-white" style="background-color: var(--theme-primary);">
                            <th class="text-left px-5 py-3 font-medium">User</th>
                            <th class="text-left px-5 py-3 font-medium">Document Type</th>
                            <th class="text-right px-5 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($pendingKycs as $kyc)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $kyc->user->name ?? 'N/A' }}</p>
                                <p class="text-xs" style="color: var(--theme-primary);">{{ '@' . ($kyc->user->username ?? '') }}</p>
                            </td>
                            <td class="px-5 py-3 text-gray-600">
                                {{ $kyc->document_type ?? 'ID Card' }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.verification.kyc.review', $kyc->id) }}" class="px-3 py-1 bg-[var(--theme-primary)] text-white text-xs font-medium rounded hover:opacity-90 transition-opacity">
                                    Review
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-xs text-gray-400">No pending KYCs</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Open Support Tickets --}}
        <div class="bg-white rounded-none shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Open Tickets</h2>
                </div>
                <a href="{{ route('admin.support.index', ['status' => 'OPEN']) }}" class="text-xs text-indigo-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-white" style="background-color: var(--theme-primary);">
                            <th class="text-left px-5 py-3 font-medium">Ticket ID</th>
                            <th class="text-left px-5 py-3 font-medium">User</th>
                            <th class="text-right px-5 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($openTickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 font-mono text-xs text-gray-600">
                                {{ $ticket->ticket_id }}
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $ticket->user->name ?? 'N/A' }}</p>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.support.show', $ticket->id) }}" class="text-[var(--theme-primary)] hover:underline text-xs font-medium">
                                    Reply
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-xs text-gray-400">No open tickets</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pending Withdrawals --}}
        <div class="bg-white rounded-none shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h2 class="text-sm font-semibold text-gray-700">Pending Withdrawals</h2>
                </div>
                <a href="{{ route('admin.withdrawals.pending') }}" class="text-xs text-indigo-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-white" style="background-color: var(--theme-primary);">
                            <th class="text-left px-5 py-3 font-medium">User</th>
                            <th class="text-right px-5 py-3 font-medium">Amount</th>
                            <th class="text-right px-5 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($pendingWithdrawalsList as $withdrawal)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-800">{{ $withdrawal->user->name ?? 'N/A' }}</p>
                                <p class="text-xs" style="color: var(--theme-primary);">{{ '@' . ($withdrawal->user->username ?? '') }}</p>
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-red-600">
                                {{ format_currency($withdrawal->amount) }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.withdrawals.pending') }}" class="px-3 py-1 bg-[var(--theme-primary)] text-white text-xs font-medium rounded hover:opacity-90 transition-opacity">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-xs text-gray-400">No pending withdrawals</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
