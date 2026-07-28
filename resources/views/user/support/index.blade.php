@extends('user.layouts.app')

@section('title', 'Support Tickets')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Support Tickets</h1>
        <p class="text-gray-600 mt-1">Get help and support from our team</p>
    </div>
    <a href="{{ route('user.support.create') }}" class="inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-[var(--primary)] text-sm font-medium text-white hover:opacity-90 focus:outline-none transition">
        Create New Ticket
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">My Tickets</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ticket ID</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Last Updated</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900">{{ $ticket->ticket_id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 truncate max-w-[250px]">{{ $ticket->subject }}</div>
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
                            <a href="{{ route('user.support.show', $ticket->id) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            You have not created any support tickets yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($tickets->hasPages())
        <div class="p-6 border-t border-gray-100">
            {{ $tickets->links() }}
        </div>
    @endif
</div>
@endsection
