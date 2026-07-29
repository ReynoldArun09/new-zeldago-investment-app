@extends('admin.layouts.app')

@section('title', 'User Detail - ' . $user->username)

@section('content')
<div class="min-h-full p-4 sm:p-6 space-y-5" style="background-color: var(--theme-bg);">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-base font-semibold text-gray-700">
            User Detail &ndash; {{ $user->username }}
        </h1>
        <form method="POST" target="_blank" action="{{ route('admin.users.impersonate', $user->username ?? $user->id) }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-1.5 text-sm border rounded-none px-3 py-1.5 hover:opacity-80 transition-opacity"
                style="color: var(--theme-primary); border-color: var(--theme-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Login as User
            </button>
        </form>
    </div>

    {{-- 8 Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label' => 'Total Invest',              'value' => format_currency($stats['totalContribution']),  'bg' => 'bg-purple-600', 'icon' => 'arrow-down'],
            ['label' => 'ROI Returns',              'value' => format_currency($stats['roiReturns']),         'bg' => 'bg-sky-500',    'icon' => 'dollar'],
            ['label' => 'Total Investments',         'value' => $stats['totalInvestments'],                                     'bg' => 'bg-indigo-700', 'icon' => 'briefcase'],
            ['label' => 'Investment Close Requests', 'value' => $stats['closeRequests'],                                        'bg' => 'bg-red-700',    'icon' => 'hand'],
            ['label' => 'Completed Investments',     'value' => $stats['completedInvestments'],                                 'bg' => 'bg-green-600',  'icon' => 'check-circle'],
            ['label' => 'My Commissions',            'value' => format_currency($stats['myCommissions']),      'bg' => 'bg-teal-600',   'icon' => 'wallet'],
            ['label' => 'Withdrawals',               'value' => format_currency($stats['withdrawals']),        'bg' => 'bg-yellow-600', 'icon' => 'landmark'],
            ['label' => 'Transactions',              'value' => $stats['transactions'],                                         'bg' => 'bg-cyan-600',   'icon' => 'arrows'],
        ];
        @endphp

        @foreach($cards as $card)
        <div class="rounded-none p-4 flex items-center justify-between {{ $card['bg'] }}">
            <div>
                <p class="text-xs text-white/80">{{ $card['label'] }}</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $card['value'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-none bg-white/20 flex items-center justify-center shrink-0">
                @if($card['icon'] === 'dollar')
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @elseif($card['icon'] === 'briefcase')
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                @elseif($card['icon'] === 'arrow-down')
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                @elseif($card['icon'] === 'hand')
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/></svg>
                @elseif($card['icon'] === 'check-circle')
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @elseif($card['icon'] === 'wallet')
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                @elseif($card['icon'] === 'landmark')
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-green-50 text-green-700 border border-green-200 text-xs px-4 py-3 rounded-none">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 text-red-700 border border-red-200 text-xs px-4 py-3 rounded-none">{{ session('error') }}</div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-2">
        {{-- Balance Add --}}
        <button type="button" id="btn-balance-add" onclick="setBalanceTab('add')"
            class="balance-tab flex items-center gap-1.5 text-sm font-medium text-white px-4 py-2 rounded-none transition-colors bg-green-500 hover:bg-green-600 opacity-80">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Balance
        </button>
        {{-- Balance Sub --}}
        <button type="button" id="btn-balance-sub" onclick="setBalanceTab('sub')"
            class="balance-tab flex items-center gap-1.5 text-sm font-medium text-white px-4 py-2 rounded-none transition-colors bg-red-500 hover:bg-red-600 opacity-80">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            Balance
        </button>
        {{-- Notifications --}}
        <button type="button" onclick="document.getElementById('notif-modal').classList.remove('hidden')"
            class="flex items-center gap-1.5 text-sm font-medium text-white px-4 py-2 rounded-none transition-colors bg-gray-500 hover:bg-gray-600 opacity-80">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Notifications
        </button>
        {{-- Ban --}}
        <form method="POST" action="{{ route('admin.users.ban', $user->username ?? $user->id) }}">
            @csrf @method('PUT')
            <button type="submit"
                class="flex items-center gap-1.5 text-sm font-medium text-white px-4 py-2 rounded-none transition-colors opacity-80 {{ $user->is_active ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-500 hover:bg-green-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                {{ $user->is_active ? 'Ban User' : 'Unban User' }}
            </button>
        </form>
        {{-- Become Agent --}}
        @if($user->account_type !== 'Agent' && $user->account_type !== 'Root Distributor')
            <div x-data="{ showAgentModal: false }">
                <button type="button" @click="showAgentModal = true"
                    class="flex items-center gap-1.5 text-sm font-medium text-white px-4 py-2 rounded-none transition-colors bg-blue-500 hover:bg-blue-600 opacity-80">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Become Agent
                </button>

                <div x-show="showAgentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" style="display: none;">
                    <div @click.outside="showAgentModal = false" class="bg-white rounded-none shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all border border-gray-200">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-blue-50/50">
                            <h3 class="text-base font-semibold text-blue-900 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Confirm Action
                            </h3>
                            <button type="button" @click="showAgentModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="px-5 py-6 text-gray-600 text-sm">
                            <p>Are you sure you want to upgrade <strong>{{ $user->username }}</strong> to an Agent?</p>
                            <p class="mt-2 text-xs text-gray-500">This action will grant them agent privileges and they will start earning commissions from their referrals.</p>
                        </div>
                        <div class="px-5 py-4 bg-gray-50/50 border-t border-gray-100 flex justify-end gap-2">
                            <button type="button" @click="showAgentModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-none shadow-sm hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <form method="POST" action="{{ route('admin.users.become-agent', $user->username ?? $user->id) }}">
                                @csrf @method('PUT')
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 border border-transparent rounded-none shadow-sm hover:bg-blue-600 transition-colors">
                                    Yes, Make Agent
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Information --}}
    <div class="bg-white rounded-none shadow-sm p-5 sm:p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-5 border-b pb-2">
            Information
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-gray-500 mb-1">Account Type</p>
                <p class="text-sm font-medium text-gray-800">{{ $user->account_type ?? 'Normal User' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Referral Code</p>
                <p class="text-sm font-medium text-gray-800 font-mono">{{ $user->referral_code ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Joined Under</p>
                <p class="text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Sponsor Username</p>
                @if($user->sponsor)
                    <a href="{{ route('admin.users.details', $user->sponsor->username ?? $user->sponsor->id) }}" class="text-sm font-medium text-blue-600 hover:underline">
                        {{ $user->sponsor->username }}
                    </a>
                @else
                    <p class="text-sm font-medium text-gray-800">N/A</p>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Sponsor Referral Code</p>
                <p class="text-sm font-medium text-gray-800 font-mono">{{ $user->sponsor->referral_code ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Network Stats</p>
                <p class="text-xs text-gray-800">Direct Referrals: <strong>{{ isset($user->directReferrals) ? $user->directReferrals->count() : 0 }}</strong></p>
                <p class="text-xs text-gray-800">Total Downline: <strong>0</strong></p>
            </div>
        </div>
    </div>

    {{-- Profile Info Form --}}
    <div class="bg-white rounded-none shadow-sm p-5 sm:p-6" x-data="{ activeDetail: null }">
        <div class="flex items-center gap-4 mb-5">
            <h2 class="text-sm font-semibold text-gray-700 whitespace-nowrap">
                Details of {{ $user->name }}
            </h2>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" @click="activeDetail = activeDetail === 'kyc' ? null : 'kyc'" 
                    class="text-sm font-medium text-white px-4 py-2 rounded-none transition-colors bg-green-500 hover:bg-green-600 opacity-80">
                    View KYC
                </button>
                <button type="button" @click="activeDetail = activeDetail === 'nominee' ? null : 'nominee'" 
                    class="text-sm font-medium text-white px-4 py-2 rounded-none transition-colors bg-red-500 hover:bg-red-600 opacity-80">
                    View Nominee
                </button>
                <button type="button" @click="activeDetail = activeDetail === 'bank' ? null : 'bank'" 
                    class="text-sm font-medium text-white px-4 py-2 rounded-none transition-colors bg-gray-500 hover:bg-gray-600 opacity-80">
                    View Bank Details
                </button>
            </div>
        </div>

        {{-- Details Dropdowns --}}
        <div x-show="activeDetail === 'kyc'" style="display: none;" class="mb-6 p-4 border border-gray-100 bg-gray-50 text-sm">
            @if($user->kyc)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><span class="text-gray-500">Document Type:</span> <span class="font-medium">{{ $user->kyc->document_type }}</span></div>
                    <div><span class="text-gray-500">Document Number:</span> <span class="font-medium">{{ $user->kyc->document_number }}</span></div>
                    <div><span class="text-gray-500">Country:</span> <span class="font-medium">{{ $user->kyc->country }}</span></div>
                    <div class="sm:col-span-2"><span class="text-gray-500">Address:</span> <span class="font-medium">{{ $user->kyc->address }}</span></div>
                    <div class="sm:col-span-2 flex gap-4 mt-2">
                        <a href="{{ Storage::url($user->kyc->document_front_proof) }}" target="_blank" class="text-blue-600 hover:underline">View Front Proof</a>
                        <a href="{{ Storage::url($user->kyc->document_back_proof) }}" target="_blank" class="text-blue-600 hover:underline">View Back Proof</a>
                    </div>
                    @if(strtolower($user->kyc->status) !== 'approved')
                    <div class="sm:col-span-2 flex gap-2 mt-4 border-t pt-4">
                        <form action="{{ route('admin.verification.kyc.status', $user->kyc->id) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 text-sm font-medium transition-colors">Approve KYC</button>
                        </form>
                        <form action="{{ route('admin.verification.kyc.status', $user->kyc->id) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 text-sm font-medium transition-colors">Reject KYC</button>
                        </form>
                    </div>
                    @else
                    <div class="sm:col-span-2 mt-4 border-t pt-4 text-sm">
                        <span class="text-gray-500">Status:</span> <span class="font-bold text-green-600 uppercase">{{ $user->kyc->status }}</span>
                    </div>
                    @endif
                </div>
            @else
                <p class="text-gray-500 italic">User has not added KYC details.</p>
            @endif
        </div>
        
        <div x-show="activeDetail === 'nominee'" style="display: none;" class="mb-6 p-4 border border-gray-100 bg-gray-50 text-sm">
            @if($user->nominee)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><span class="text-gray-500">Full Name:</span> <span class="font-medium">{{ $user->nominee->full_name }}</span></div>
                    <div><span class="text-gray-500">Relation:</span> <span class="font-medium">{{ $user->nominee->relation }}</span></div>
                    <div><span class="text-gray-500">Date of Birth:</span> <span class="font-medium">{{ $user->nominee->date_of_birth }}</span></div>
                    <div><span class="text-gray-500">Identity Proof Type:</span> <span class="font-medium">{{ $user->nominee->identity_proof_type }}</span></div>
                    <div><span class="text-gray-500">Identity Proof Number:</span> <span class="font-medium">{{ $user->nominee->identity_proof_number }}</span></div>
                    <div class="sm:col-span-2 mt-2">
                        <a href="{{ Storage::url($user->nominee->identity_proof_document) }}" target="_blank" class="text-blue-600 hover:underline">View Identity Document</a>
                    </div>
                    @if(strtolower($user->nominee->status) !== 'approved')
                    <div class="sm:col-span-2 flex gap-2 mt-4 border-t pt-4">
                        <form action="{{ route('admin.verification.nominee.status', $user->nominee->id) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 text-sm font-medium transition-colors">Approve Nominee</button>
                        </form>
                        <form action="{{ route('admin.verification.nominee.status', $user->nominee->id) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 text-sm font-medium transition-colors">Reject Nominee</button>
                        </form>
                    </div>
                    @else
                    <div class="sm:col-span-2 mt-4 border-t pt-4 text-sm">
                        <span class="text-gray-500">Status:</span> <span class="font-bold text-green-600 uppercase">{{ $user->nominee->status }}</span>
                    </div>
                    @endif
                </div>
            @else
                <p class="text-gray-500 italic">User has not added Nominee details.</p>
            @endif
        </div>

        <div x-show="activeDetail === 'bank'" style="display: none;" class="mb-6 p-4 border border-gray-100 bg-gray-50 text-sm">
            @if($user->bankDetail)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><span class="text-gray-500">Account Name:</span> <span class="font-medium">{{ $user->bankDetail->name }}</span></div>
                    <div><span class="text-gray-500">Bank Name:</span> <span class="font-medium">{{ $user->bankDetail->bank_name }}</span></div>
                    <div><span class="text-gray-500">Account Number:</span> <span class="font-medium">{{ $user->bankDetail->account_number }}</span></div>
                    <div><span class="text-gray-500">IFSC Code:</span> <span class="font-medium">{{ $user->bankDetail->ifsc_code }}</span></div>
                    <div><span class="text-gray-500">UPI ID:</span> <span class="font-medium">{{ $user->bankDetail->upi_id ?? 'N/A' }}</span></div>
                    <div><span class="text-gray-500">UPI Number:</span> <span class="font-medium">{{ $user->bankDetail->upi_number ?? 'N/A' }}</span></div>
                    @if($user->bankDetail->proof_image)
                    <div class="sm:col-span-2 mt-2">
                        <a href="{{ Storage::url($user->bankDetail->proof_image) }}" target="_blank" class="text-blue-600 hover:underline">View Bank Proof</a>
                    </div>
                    @endif
                    @if(strtolower($user->bankDetail->status) !== 'approved')
                    <div class="sm:col-span-2 flex gap-2 mt-4 border-t pt-4">
                        <form action="{{ route('admin.verification.bank.status', $user->bankDetail->id) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 text-sm font-medium transition-colors">Approve Bank Details</button>
                        </form>
                        <form action="{{ route('admin.verification.bank.status', $user->bankDetail->id) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 text-sm font-medium transition-colors">Reject Bank Details</button>
                        </form>
                    </div>
                    @else
                    <div class="sm:col-span-2 mt-4 border-t pt-4 text-sm">
                        <span class="text-gray-500">Status:</span> <span class="font-bold text-green-600 uppercase">{{ $user->bankDetail->status }}</span>
                    </div>
                    @endif
                </div>
            @else
                <p class="text-gray-500 italic">User has not added Bank details.</p>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user->username ?? $user->id) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ $user->name ?? '' }}" required
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ $user->email }}" required
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <div class="flex items-center border border-gray-200 rounded-none overflow-hidden focus-within:border-[var(--theme-primary)] transition-colors">
                        <span class="px-3 py-2 text-sm text-gray-500 bg-gray-50 border-r border-gray-200">+</span>
                        <input type="tel" name="phone" value="{{ ltrim($user->phone ?? '', '+') }}"
                            class="flex-1 px-3 py-2 text-sm text-gray-700 outline-none bg-transparent">
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Address</label>
                    <input type="text" name="address" value="{{ $user->address ?? '' }}"
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">City</label>
                    <input type="text" name="city" value="{{ $user->city ?? '' }}"
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">State</label>
                    <input type="text" name="state" value="{{ $user->state ?? '' }}"
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Zip / Postal</label>
                    <input type="text" name="zip" value="{{ $user->zip ?? '' }}"
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Country <span class="text-red-500">*</span></label>
                    <select name="country" class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors bg-white">
                        <option value="">Select country</option>
                        @foreach(['Afghanistan','Albania','Algeria','Argentina','Australia','Austria','Bangladesh','Belgium','Brazil','Canada','China','Colombia','Costa Rica','Denmark','Egypt','Ethiopia','Finland','France','Gambia','Germany','Ghana','Greece','India','Indonesia','Iran','Iraq','Ireland','Italy','Japan','Jordan','Kenya','Kuwait','Malaysia','Mexico','Morocco','Netherlands','New Zealand','Nigeria','Norway','Pakistan','Peru','Philippines','Poland','Portugal','Romania','Russia','Saudi Arabia','South Africa','South Korea','Spain','Sweden','Switzerland','Tanzania','Thailand','Turkey','Uganda','Ukraine','United Kingdom','United States','Venezuela','Vietnam','Zambia','Zimbabwe'] as $country)
                            <option value="{{ $country }}" {{ ($user->country ?? '') === $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Verification Toggle Buttons --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5">
                {{-- Email Verification --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Email Verification</label>
                    @php $emailVerified = !empty($user->email_verified_at); @endphp
                    <button type="button" id="btn-email"
                        onclick="toggleVerify('email', '{{ $emailVerified ? '1' : '0' }}')"
                        class="w-full py-2 rounded-none text-sm font-medium text-white transition-colors {{ $emailVerified ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }}">
                        {{ $emailVerified ? 'Verified' : 'Unverified' }}
                    </button>
                    <input type="hidden" name="email_verified" id="val-email" value="{{ $emailVerified ? '1' : '0' }}">
                </div>
                {{-- Mobile Verification --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Mobile Verification</label>
                    @php $mobileVerified = (bool)($user->mobile_verified ?? false); @endphp
                    <button type="button" id="btn-mobile"
                        onclick="toggleVerify('mobile', '{{ $mobileVerified ? '1' : '0' }}')"
                        class="w-full py-2 rounded-none text-sm font-medium text-white transition-colors {{ $mobileVerified ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }}">
                        {{ $mobileVerified ? 'Verified' : 'Unverified' }}
                    </button>
                    <input type="hidden" name="mobile_verified" id="val-mobile" value="{{ $mobileVerified ? '1' : '0' }}">
                </div>
                {{-- 2FA --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1">2FA Verification</label>
                    @php $twoFa = (bool)($user->two_fa ?? false); @endphp
                    <button type="button" id="btn-twofa"
                        onclick="toggleVerify('twofa', '{{ $twoFa ? '1' : '0' }}')"
                        class="w-full py-2 rounded-none text-sm font-medium text-white transition-colors {{ $twoFa ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }}">
                        {{ $twoFa ? 'Enabled' : 'Disable' }}
                    </button>
                    <input type="hidden" name="two_fa" id="val-twofa" value="{{ $twoFa ? '1' : '0' }}">
                </div>
                {{-- KYC --}}
                <div>
                    <label class="block text-xs text-gray-500 mb-1">KYC</label>
                    @php
                        $kyc = strtoupper($user->kyc_status ?? 'UNVERIFIED');
                        $kycClass = $kyc === 'VERIFIED' ? 'bg-green-500 hover:bg-green-600' : ($kyc === 'PENDING' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-red-500 hover:bg-red-600');
                    @endphp
                    <button type="button" id="btn-kyc" onclick="cycleKyc()"
                        class="w-full py-2 rounded-none text-sm font-medium text-white transition-colors uppercase {{ $kycClass }}"
                        data-kyc="{{ $kyc }}">
                        {{ $kyc }}
                    </button>
                    <input type="hidden" name="kyc_status" id="val-kyc" value="{{ $kyc }}">
                </div>
            </div>

            <button type="submit"
                class="mt-5 w-full text-white text-sm font-medium py-2.5 rounded-none transition-colors hover:opacity-90"
                style="background-color: var(--theme-primary);">
                Submit
            </button>
        </form>
    </div>

    {{-- Update Password Form --}}
    <div class="bg-white rounded-none shadow-sm p-5 sm:p-6 mt-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-5">
            Change Password
        </h2>
        <form method="POST" action="{{ route('admin.users.updatePassword', $user->username ?? $user->id) }}">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required minlength="8"
                        class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm text-gray-700 outline-none focus:border-[var(--theme-primary)] transition-colors">
                </div>
            </div>

            <button type="submit"
                class="mt-5 text-white text-sm font-medium py-2 px-6 rounded-none transition-colors hover:opacity-90"
                style="background-color: var(--theme-primary);">
                Update Password
            </button>
        </form>
    </div>

</div>

{{-- Notification Modal --}}
<div id="notif-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-none shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Send Notification</h3>
            <button onclick="document.getElementById('notif-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.users.notification', $user->username ?? $user->id) }}" class="p-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
                <input type="text" name="title" required
                    class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm focus:border-[var(--theme-primary)] outline-none"
                    placeholder="Notification Title">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Message</label>
                <textarea name="message" required rows="4"
                    class="w-full border border-gray-200 rounded-none px-3 py-2 text-sm focus:border-[var(--theme-primary)] outline-none resize-none"
                    placeholder="Enter message here..."></textarea>
            </div>
            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('notif-modal').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-none transition-colors">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 text-sm text-white rounded-none transition-colors"
                    style="background-color: var(--theme-primary);">Send</button>
            </div>
        </form>
    </div>
</div>

<script>
// Notification modal toggle
document.querySelector('[onclick*="notif-modal"]') && document.querySelector('[onclick*="notif-modal"]').addEventListener('click', function() {
    document.getElementById('notif-modal').style.display = 'flex';
});

function setBalanceTab(tab) {
    document.getElementById('btn-balance-add').classList.toggle('ring-2', tab === 'add');
    document.getElementById('btn-balance-add').classList.toggle('ring-offset-1', tab === 'add');
    document.getElementById('btn-balance-sub').classList.toggle('ring-2', tab === 'sub');
    document.getElementById('btn-balance-sub').classList.toggle('ring-offset-1', tab === 'sub');
}

function toggleVerify(field, current) {
    const isOn = current === '1' || document.getElementById('val-' + field).value === '1';
    const newVal = isOn ? '0' : '1';
    document.getElementById('val-' + field).value = newVal;
    const btn = document.getElementById('btn-' + field);
    if (newVal === '1') {
        btn.className = btn.className.replace('bg-red-500 hover:bg-red-600', 'bg-green-500 hover:bg-green-600');
        btn.textContent = field === 'twofa' ? 'Enabled' : 'Verified';
    } else {
        btn.className = btn.className.replace('bg-green-500 hover:bg-green-600', 'bg-red-500 hover:bg-red-600');
        btn.textContent = field === 'twofa' ? 'Disable' : 'Unverified';
    }
    // Update the current value reference on next call
    btn.setAttribute('onclick', "toggleVerify('" + field + "', '" + newVal + "')");
}

function cycleKyc() {
    const cycle = { 'UNVERIFIED': 'PENDING', 'PENDING': 'VERIFIED', 'VERIFIED': 'UNVERIFIED' };
    const colorFrom = { 'UNVERIFIED': 'bg-red-500 hover:bg-red-600', 'PENDING': 'bg-yellow-500 hover:bg-yellow-600', 'VERIFIED': 'bg-green-500 hover:bg-green-600' };
    const colorTo = { 'UNVERIFIED': 'bg-yellow-500 hover:bg-yellow-600', 'PENDING': 'bg-green-500 hover:bg-green-600', 'VERIFIED': 'bg-red-500 hover:bg-red-600' };
    const btn = document.getElementById('btn-kyc');
    const current = btn.dataset.kyc;
    const next = cycle[current];
    btn.className = btn.className.replace(colorFrom[current], colorTo[current]);
    btn.textContent = next;
    btn.dataset.kyc = next;
    document.getElementById('val-kyc').value = next;
}
</script>
@endsection
