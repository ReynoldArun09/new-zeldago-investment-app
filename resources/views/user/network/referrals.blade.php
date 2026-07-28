@extends('user.layouts.app')

@section('title', 'My Referrals')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Network</h1>
        <p class="text-gray-600 mt-1">Manage your sponsor and view your direct referrals.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Sponsor Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center">
        <div class="w-16 h-16 bg-indigo-50 text-primary rounded-full flex items-center justify-center mb-4 border border-indigo-100">
            <i class="ph ph-crown text-3xl"></i>
        </div>
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">My Sponsor</h3>
        
        @if($user->sponsor)
            <div class="mt-2">
                <p class="text-lg font-bold text-gray-900">{{ $user->sponsor->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->sponsor->username }}</p>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 w-full">
                <p class="text-xs text-gray-400">Account Type: <span class="font-medium text-gray-700">{{ $user->sponsor->account_type }}</span></p>
            </div>
        @else
            <div class="mt-2">
                <p class="text-lg font-bold text-gray-900">None</p>
                <p class="text-sm text-gray-500">You joined directly.</p>
            </div>
        @endif
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-900">Direct Referrals</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-gray-50/50 text-gray-500 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Joined Date</th>
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
                        <td class="px-6 py-4 text-gray-600">{{ $referral->email }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $referral->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
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
