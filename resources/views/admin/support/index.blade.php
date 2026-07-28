@extends('admin.layouts.app')

@section('title', 'Support Tickets')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Support Tickets</h1>
        <p class="text-gray-600 mt-1">Manage user support requests</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h2 class="text-lg font-semibold text-gray-900">All Tickets</h2>
        <form action="{{ route('admin.support.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, Username..." class="w-full sm:w-48 px-3 py-1.5 text-sm border border-gray-200 rounded focus:outline-none focus:border-[var(--theme-primary)] bg-white text-gray-700 placeholder-gray-400">
            
            <select name="status" class="px-3 py-1.5 text-sm border border-gray-200 rounded focus:outline-none focus:border-[var(--theme-primary)] bg-white text-gray-700">
                <option value="">All Statuses</option>
                <option value="OPEN" {{ request('status') == 'OPEN' ? 'selected' : '' }}>Open</option>
                <option value="ANSWERED" {{ request('status') == 'ANSWERED' ? 'selected' : '' }}>Answered</option>
                <option value="CLOSED" {{ request('status') == 'CLOSED' ? 'selected' : '' }}>Closed</option>
            </select>
            
            <select name="priority" class="px-3 py-1.5 text-sm border border-gray-200 rounded focus:outline-none focus:border-[var(--theme-primary)] bg-white text-gray-700">
                <option value="">All Priorities</option>
                <option value="LOW" {{ request('priority') == 'LOW' ? 'selected' : '' }}>Low</option>
                <option value="MEDIUM" {{ request('priority') == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                <option value="HIGH" {{ request('priority') == 'HIGH' ? 'selected' : '' }}>High</option>
            </select>

            <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white rounded transition-opacity hover:opacity-90" style="background-color: var(--theme-primary);">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'status', 'priority']))
                <a href="{{ route('admin.support.index') }}" class="px-3 py-1.5 text-sm font-medium text-gray-600 bg-gray-100 rounded border border-gray-200 hover:bg-gray-200 transition-colors">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-white" style="background-color: var(--theme-primary);">
                    <th class="text-left px-5 py-3 font-medium">Ticket ID</th>
                    <th class="text-left px-5 py-3 font-medium">User</th>
                    <th class="text-left px-5 py-3 font-medium">Subject</th>
                    <th class="text-left px-5 py-3 font-medium">Priority</th>
                    <th class="text-left px-5 py-3 font-medium">Status</th>
                    <th class="text-left px-5 py-3 font-medium">Last Updated</th>
                    <th class="text-right px-5 py-3 font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900">{{ $ticket->ticket_id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                    {{ substr($ticket->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $ticket->user->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 truncate max-w-[200px]">{{ $ticket->subject }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($ticket->priority === 'HIGH')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    High
                                </span>
                            @elseif($ticket->priority === 'MEDIUM')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Medium
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Low
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($ticket->status === 'OPEN')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Open
                                </span>
                            @elseif($ticket->status === 'ANSWERED')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    Answered
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Closed
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $ticket->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.support.show', $ticket->id) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No support tickets found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($tickets->hasPages())
        <div class="p-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-600">
            <span>
                Showing {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} of {{ $tickets->total() }} results
            </span>
            <div class="flex items-center gap-1">
                {{-- Prev --}}
                @if($tickets->onFirstPage())
                    <span class="px-3 py-1 border border-gray-200 rounded-none opacity-40 cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $tickets->previousPageUrl() }}" class="px-3 py-1 border border-gray-200 rounded-none hover:bg-gray-50 transition-colors">Prev</a>
                @endif

                {{-- Page numbers --}}
                @foreach($tickets->getUrlRange(1, $tickets->lastPage()) as $page => $url)
                    @if($page == $tickets->currentPage())
                        <span class="px-3 py-1 border rounded-none text-white" style="background-color: var(--theme-primary); border-color: var(--theme-primary);">{{ $page }}</span>
                    @elseif($page == 1 || $page == $tickets->lastPage() || abs($page - $tickets->currentPage()) <= 1)
                        <a href="{{ $url }}" class="px-3 py-1 border border-gray-200 rounded-none hover:bg-gray-50 transition-colors">{{ $page }}</a>
                    @elseif(abs($page - $tickets->currentPage()) == 2)
                        <span class="px-2">…</span>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($tickets->hasMorePages())
                    <a href="{{ $tickets->nextPageUrl() }}" class="px-3 py-1 border border-gray-200 rounded-none hover:bg-gray-50 transition-colors">Next</a>
                @else
                    <span class="px-3 py-1 border border-gray-200 rounded-none opacity-40 cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
