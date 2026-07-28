@extends('user.layouts.app')

@section('title', 'Ticket #' . $ticket->ticket_id)

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Ticket #{{ $ticket->ticket_id }}</h1>
        <p class="text-gray-600 mt-1">{{ $ticket->subject }}</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('user.support.index') }}" class="inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">
            Back to Tickets
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Conversation</h2>
            </div>
            <div class="p-6 space-y-6 max-h-[600px] overflow-y-auto">
                @foreach($ticket->messages as $msg)
                    @if($msg->user_id)
                        <!-- User Message -->
                        <div class="flex gap-4 flex-row-reverse">
                            <div class="w-10 h-10 rounded-full bg-[var(--primary)] text-white flex-shrink-0 flex items-center justify-center font-bold text-sm">
                                {{ substr($msg->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="bg-indigo-50/50 rounded-2xl p-4 border border-indigo-100 text-right">
                                    <div class="flex items-center justify-between flex-row-reverse mb-2">
                                        <span class="font-medium text-[var(--primary)]">You</span>
                                        <span class="text-xs text-gray-500">{{ $msg->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <p class="text-gray-700 whitespace-pre-wrap text-left">{{ $msg->message }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Admin Message -->
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex-shrink-0 flex items-center justify-center font-bold text-sm">
                                A
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-gray-900">Support Team</span>
                                        <span class="text-xs text-gray-500">{{ $msg->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <p class="text-gray-700 whitespace-pre-wrap">{{ $msg->message }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            
            @if($ticket->status !== 'CLOSED')
            <div class="p-6 border-t border-gray-100 bg-gray-50/50">
                <form action="{{ route('user.support.reply', $ticket->id) }}" method="POST">
                    @csrf
                    <div>
                        <label for="message" class="sr-only">Reply</label>
                        <textarea id="message" name="message" rows="4" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Type your reply here..." required></textarea>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-[var(--primary)] text-sm font-medium text-white hover:opacity-90 focus:outline-none transition">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="p-6 border-t border-gray-100 bg-gray-50/50 text-center text-gray-500">
                This ticket has been closed. If you need further assistance, please open a new ticket.
            </div>
            @endif
        </div>
    </div>

    <!-- Ticket Info Sidebar -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Ticket Information</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        @if($ticket->status === 'OPEN')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Open</span>
                        @elseif($ticket->status === 'ANSWERED')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Answered</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Closed</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500">Priority</dt>
                    <dd class="mt-1">
                        @if($ticket->priority === 'HIGH')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">High</span>
                        @elseif($ticket->priority === 'MEDIUM')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Medium</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Low</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $ticket->created_at->format('M d, Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500">Last Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $ticket->updated_at->diffForHumans() }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection
