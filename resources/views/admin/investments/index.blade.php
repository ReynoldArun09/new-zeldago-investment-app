@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">
    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-lg font-bold text-gray-800">{{ $title }}</h2>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('admin.investments.index', ['status' => $status]) }}" method="GET" class="flex gap-2">
                    <select name="month" class="px-3 py-2 text-sm bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-lg focus:outline-none focus:border-indigo-300 focus:ring-2 focus:ring-indigo-100 transition-colors font-medium">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="year" class="px-3 py-2 text-sm bg-violet-50 border border-violet-100 text-violet-700 rounded-lg focus:outline-none focus:border-violet-300 focus:ring-2 focus:ring-violet-100 transition-colors font-medium">
                        <option value="">All Years</option>
                        @foreach(range(date('Y'), 2023) as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>

                    <div class="flex">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="TrxID / Username" class="w-full md:w-64 px-4 py-2 text-sm border border-gray-200 rounded-l-lg focus:outline-none focus:border-[var(--theme-primary)] border-r-0">
                        <button type="submit" class="px-4 py-2 bg-[var(--theme-primary)] text-white rounded-r-lg hover:opacity-90 transition-opacity">
                            <i class="ph ph-magnifying-glass"></i>
                        </button>
                    </div>
                </form>

                <a href="{{ route('admin.investments.export', ['status' => $status ?? 'all', 'month' => request('month'), 'year' => request('year'), 'search' => request('search')]) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                    <i class="ph ph-download-simple"></i> Export CSV
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-white">
                <thead class="bg-[var(--theme-primary)] font-medium">
                    <tr>
                        <th class="px-6 py-3.5">Trx ID</th>
                        <th class="px-6 py-3.5">Started</th>
                        <th class="px-6 py-3.5">User</th>
                        <th class="px-6 py-3.5">Initial Deposit</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 divide-y divide-gray-100">
                    @forelse($investments as $inv)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-xs text-[var(--theme-primary)] uppercase">
                                {{ $inv->trx_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $inv->created_at->format('m/d/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 text-sm">{{ $inv->user->name ?? 'User' }}</div>
                                <a href="{{ $inv->user && $inv->user->username ? route('admin.users.details', $inv->user->username) : '#' }}" class="text-xs text-[var(--theme-primary)] hover:underline">
                                    {{ $inv->user && $inv->user->username ? '@' . $inv->user->username : '@unknown' }}
                                </a>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-800">
                                {{ format_currency($inv->amount) }}
                            </td>
                            <td class="px-6 py-4">
                                @if(strtoupper($inv->status) === 'PENDING')
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-amber-500 border border-amber-400 uppercase">
                                        Pending
                                    </span>
                                @elseif(strtoupper($inv->status) === 'ACTIVE')
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-indigo-500 border border-indigo-400 uppercase">
                                        Active
                                    </span>
                                @elseif(strtoupper($inv->status) === 'COMPLETED')
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-emerald-500 border border-emerald-400 uppercase">
                                        Completed
                                    </span>
                                @elseif(strtoupper($inv->status) === 'CLOSED')
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-gray-500 border border-gray-400 uppercase">
                                        Closed
                                    </span>
                                @elseif(strtoupper($inv->status) === 'CLOSE_REQUEST')
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-orange-500 border border-orange-400 uppercase">
                                        Close Request
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-bold bg-white text-red-500 border border-red-400 uppercase">
                                        {{ $inv->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.investments.show', $inv->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-indigo-200 rounded-md text-xs font-medium text-[var(--theme-primary)] bg-white hover:bg-indigo-50 focus:outline-none transition-colors">
                                    Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                No investments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($investments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Showing {{ $investments->firstItem() ?? 0 }} to {{ $investments->lastItem() ?? 0 }} of {{ $investments->total() }} results
                </span>
                <div>
                    {{ $investments->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        @else
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-sm text-gray-500">
                    Showing {{ $investments->count() }} to {{ $investments->count() }} of {{ $investments->count() }} results
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
