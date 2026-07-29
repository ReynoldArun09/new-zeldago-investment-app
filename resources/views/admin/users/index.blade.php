@extends('admin.layouts.app')

@section('title', 'All Users')

@section('content')
<div class="min-h-full p-4 sm:p-6">
    <div class="bg-white rounded-none shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <h1 class="text-base font-semibold text-gray-700">All Users</h1>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 border border-gray-200 rounded-none px-3 py-1.5 w-full sm:w-64 focus-within:border-[var(--theme-primary)] transition-colors">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Username / Email"
                        class="flex-1 text-sm text-gray-700 placeholder-gray-400 outline-none bg-transparent min-w-0"
                    >
                    <button type="submit" class="shrink-0 w-6 h-6 rounded-none flex items-center justify-center" style="background-color: var(--theme-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M16.5 10.5a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                    </button>
                </form>
                <a href="{{ route('admin.users.create') }}" class="shrink-0 px-3 py-1.5 text-sm font-medium text-white transition-opacity hover:opacity-90 rounded-none whitespace-nowrap" style="background-color: var(--theme-primary);">
                    + Add User
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-white" style="background-color: var(--theme-primary);">
                        <th class="text-left px-5 py-3 font-medium">User</th>
                        <th class="text-left px-5 py-3 font-medium">Contact</th>
                        <th class="text-left px-4 py-3 font-medium">User Info</th>
                        <th class="text-left px-5 py-3 font-medium">Joined At</th>
                        <th class="text-right px-5 py-3 font-medium">ROI</th>
                        <th class="text-right px-5 py-3 font-medium">Commission</th>
                        <th class="text-center px-5 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-gray-800 text-sm">{{ $user->name }}</p>
                            <p class="text-xs" style="color: var(--theme-primary);">{{ '@' . ($user->username ?? $user->id) }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-400 space-y-0.5">
                            <p>{{ $user->email }}</p>
                            <p>{{ $user->phone ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-xs space-y-0.5">
                            <p><span class="font-medium text-gray-600">Type:</span> <span class="{{ $user->account_type == 'Root Distributor' ? 'text-purple-600 font-semibold' : 'text-gray-500' }}">{{ $user->account_type }}</span></p>
                            <p><span class="font-medium text-gray-600">Code:</span> {{ $user->referral_code ?? '—' }}</p>
                            <p><span class="font-medium text-gray-600">Sponsor:</span> {!! $user->sponsor ? $user->sponsor->name . ' (<a href="'.route('admin.users.details', $user->sponsor->username ?? $user->sponsor->id).'" class="hover:underline text-blue-600">@'.$user->sponsor->username.'</a>)' : '<span class="text-gray-400">None</span>' !!}</p>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                            <p>{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</p>
                            <p class="text-gray-400">{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}</p>
                        </td>
                        @php
                            $roiAmount = \App\Models\Transaction::where('user_id', $user->id)->where('type', 'ROI')->sum('amount');
                            $commAmount = \App\Models\Transaction::where('user_id', $user->id)->where('type', 'COMMISSION')->sum('amount');
                        @endphp
                        <td class="px-5 py-3.5 text-right">
                            @if($roiAmount > 0)
                                <p class="text-sm font-semibold text-gray-700">{{ format_currency($roiAmount) }}</p>
                            @else
                                <p class="text-sm text-gray-400">—</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($commAmount > 0)
                                <p class="text-sm font-semibold text-gray-700">{{ format_currency($commAmount) }}</p>
                            @else
                                <p class="text-sm text-gray-400">—</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('admin.users.details', $user->username ?? $user->id) }}"
                               class="inline-flex items-center gap-1 text-xs font-medium border rounded-none px-3 py-1.5 hover:opacity-80 transition-opacity"
                               style="color: var(--theme-primary); border-color: var(--theme-primary);">
                                Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400 text-sm">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->lastPage() > 1)
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 text-xs text-gray-500">
            <span>
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} results
            </span>
            <div class="flex items-center gap-1">
                {{-- Prev --}}
                @if($users->onFirstPage())
                    <span class="px-3 py-1 border border-gray-200 rounded-none opacity-40 cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="px-3 py-1 border border-gray-200 rounded-none hover:bg-gray-50 transition-colors">Prev</a>
                @endif

                {{-- Page numbers --}}
                @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if($page == $users->currentPage())
                        <span class="px-3 py-1 border rounded-none text-white" style="background-color: var(--theme-primary); border-color: var(--theme-primary);">{{ $page }}</span>
                    @elseif($page == 1 || $page == $users->lastPage() || abs($page - $users->currentPage()) <= 1)
                        <a href="{{ $url }}" class="px-3 py-1 border border-gray-200 rounded-none hover:bg-gray-50 transition-colors">{{ $page }}</a>
                    @elseif(abs($page - $users->currentPage()) == 2)
                        <span class="px-2">…</span>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="px-3 py-1 border border-gray-200 rounded-none hover:bg-gray-50 transition-colors">Next</a>
                @else
                    <span class="px-3 py-1 border border-gray-200 rounded-none opacity-40 cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
