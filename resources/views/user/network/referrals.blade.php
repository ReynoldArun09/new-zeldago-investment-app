@extends('user.layouts.app')

@section('title', 'My Referrals')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Network</h1>
        <p class="text-gray-600 mt-1">Manage your sponsor and view your direct referrals.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
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

    <!-- Referral Stats -->
    <div class="lg:col-span-2 bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-2xl shadow-sm border border-indigo-800 p-6 sm:p-8 text-white relative overflow-hidden flex flex-col justify-center">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <h3 class="text-indigo-200 font-medium mb-1">Total Direct Referrals</h3>
                <div class="text-4xl font-black">{{ $user->directReferrals->count() }}</div>
                <p class="text-sm text-indigo-300 mt-2">Share your referral link to grow your network.</p>
            </div>
            
            <div class="bg-indigo-950/50 backdrop-blur-sm p-4 rounded-xl border border-indigo-700/50">
                <p class="text-xs text-indigo-300 mb-1 font-medium uppercase tracking-wider">Your Referral Link</p>
                <div class="flex items-center gap-2">
                    <code class="text-sm text-indigo-100 select-all">{{ url('/register?ref=' . $user->referral_code) }}</code>
                </div>
            </div>
        </div>
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
