@extends('user.layouts.app')

@section('title', 'My Referrals')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Network</h1>
        <p class="text-gray-600 mt-1">Manage your sponsor and view your investors.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Sponsor Card -->
    <div class="text-white flex justify-between shadow-sm h-24 rounded-sm overflow-hidden" style="background-color: #00a65a;">
        <div class="p-4 flex flex-col justify-center">
            <p class="text-xs mb-1 font-medium opacity-90 uppercase tracking-wider">My Sponsor</p>
            @if($user->sponsor)
                <p class="text-xl font-bold tracking-wide truncate">{{ $user->sponsor->name }}</p>
                <p class="text-xs opacity-90 truncate">{{ $user->sponsor->username }} ({{ $user->sponsor->account_type }})</p>
            @else
                <p class="text-xl font-bold tracking-wide">None</p>
                <p class="text-xs opacity-90">You joined directly.</p>
            @endif
        </div>
        <div class="w-16 flex items-center justify-center shrink-0" style="background-color: rgba(0,0,0,0.1);">
            <i class="ph ph-crown text-2xl opacity-90"></i>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-900">My Investors</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead>
                <tr class="text-white text-xs font-bold uppercase tracking-wider" style="background-color: var(--primary);">
                    <th class="px-6 py-3 font-medium">Investor</th>
                    <th class="px-6 py-3 font-medium">City</th>
                    <th class="px-6 py-3 font-medium">Phone Number</th>
                    <th class="px-6 py-3 font-medium text-center">Investments</th>
                    <th class="px-6 py-3 font-medium text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($user->directReferrals as $referral)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-primary flex items-center justify-center font-bold text-xs">
                                    {{ substr($referral->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $referral->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $referral->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $referral->city ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $referral->phone ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center font-medium text-gray-900">{{ format_currency($referral->investments->sum('amount')) }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('user.network.investor.investments', $referral->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-indigo-200 rounded-md text-xs font-medium text-[var(--primary)] bg-white hover:bg-indigo-50 focus:outline-none transition-colors">
                                Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="ph ph-users text-4xl text-gray-300 mb-3"></i>
                                <p class="font-medium text-gray-600">No referrals yet.</p>
                                <p class="text-sm mt-1">Share your link to start building your network!</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
