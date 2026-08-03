@extends('admin.layouts.app')

@section('title', 'All Agents')

@section('content')
<div class="min-h-full p-4 sm:p-6">
    <div class="bg-white rounded-none shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-gray-100">
            <h1 class="text-base font-semibold text-gray-700">All Agents</h1>
                <form method="GET" action="{{ route('admin.users.agents') }}" class="flex items-center gap-2 border border-gray-200 rounded-none px-3 py-1.5 w-full sm:w-64 focus-within:border-[var(--theme-primary)] transition-colors">
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
                        <td class="px-5 py-3.5 text-xs text-gray-600 space-y-0.5">
                            <p>{{ $user->email }}</p>
                            <p>{{ $user->mobile ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-xs space-y-0.5">
                            <p><span class="font-medium text-gray-600">Type:</span> <span class="{{ $user->account_type == 'Root Distributor' ? 'text-purple-600 font-semibold' : 'text-gray-500' }}">{{ $user->account_type }}</span></p>
                            <p><span class="font-medium text-gray-600">Code:</span> {{ $user->referral_code ?? '—' }}</p>
                            <p><span class="font-medium text-gray-600">Sponsor:</span> {!! $user->sponsor ? '<a href="'.route('admin.users.details', $user->sponsor->username ?? $user->sponsor->id).'" class="hover:underline text-blue-600">@'.$user->sponsor->username.'</a>' : '<span class="text-gray-400">None</span>' !!}</p>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-600 whitespace-nowrap">
                            <p>{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</p>
                            <p class="text-gray-400">{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}</p>
                        </td>
                        @php
                            $commAmount = \App\Models\Transaction::where('user_id', $user->id)->where('type', 'COMMISSION')->sum('amount');
                        @endphp
                        <td class="px-5 py-3.5 text-right">
                            @if($commAmount > 0)
                                <p class="text-sm font-semibold text-gray-700">{{ format_currency($commAmount) }}</p>
                            @else
                                <p class="text-sm text-gray-400">—</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.details', $user->username ?? $user->id) }}"
                                   class="inline-flex items-center gap-1 text-xs font-medium border rounded-none px-3 py-1.5 hover:opacity-80 transition-opacity"
                                   style="color: var(--theme-primary); border-color: var(--theme-primary);">
                                    Details
                                </a>
                                <form method="POST" target="_blank" action="{{ route('admin.users.impersonate', $user->username ?? $user->id) }}">
                                    @csrf
                                    <button type="submit" title="Login as User"
                                        class="inline-flex items-center gap-1 text-xs font-medium border rounded-none px-3 py-1.5 hover:opacity-80 transition-opacity text-green-600 border-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        Login
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-gray-400 text-sm">No agents found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                </span>
                <div>
                    {{ $users->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        @else
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Showing {{ $users->count() }} to {{ $users->count() }} of {{ $users->count() }} results
                </span>
                <div class="flex gap-1">
                    <button disabled class="px-3 py-1 border border-gray-100 rounded-md bg-gray-50 text-gray-400 text-sm">&lt;</button>
                    <button class="px-3 py-1 border border-[var(--theme-primary)] rounded-md bg-[var(--theme-primary)] text-white text-sm">1</button>
                    <button disabled class="px-3 py-1 border border-gray-100 rounded-md bg-gray-50 text-gray-400 text-sm">&gt;</button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
