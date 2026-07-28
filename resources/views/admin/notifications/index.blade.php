@extends('admin.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="min-h-full p-4 sm:p-6 space-y-6 max-w-4xl mx-auto">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600 mt-1">Stay updated with system activity.</p>
        </div>
        
        @if(auth('admin')->user()->unreadNotifications->count() > 0)
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm font-medium text-white hover:opacity-90 transition-colors bg-[var(--theme-primary)] px-4 py-2 rounded-lg">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-none shadow-sm border border-gray-100 overflow-hidden">
        <ul class="divide-y divide-gray-50">
            @forelse($notifications as $notification)
                <li class="p-6 transition-colors {{ $notification->read_at ? 'bg-white' : 'bg-gray-50' }}">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $notification->read_at ? 'bg-gray-100 text-gray-500' : 'bg-indigo-100 text-indigo-600' }}" style="{{ !$notification->read_at ? 'color: var(--theme-primary); background-color: color-mix(in srgb, var(--theme-primary) 10%, transparent);' : '' }}">
                            @if(isset($notification->data['icon']))
                                <i class="ph {{ $notification->data['icon'] }} text-xl"></i>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                        </div>
                        
                        <div class="flex-1">
                            <h4 class="text-sm font-bold {{ $notification->read_at ? 'text-gray-700' : 'text-gray-900' }}">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h4>
                            <p class="text-sm mt-1 {{ $notification->read_at ? 'text-gray-500' : 'text-gray-700' }}">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <span class="text-xs text-gray-400 mt-2 block">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>

                        @if(!$notification->read_at)
                            <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Mark as read">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </li>
            @empty
                <li class="p-12 text-center text-gray-500">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <p class="font-medium text-gray-600">You're all caught up!</p>
                    <p class="text-sm mt-1">You have no notifications at the moment.</p>
                </li>
            @endforelse
        </ul>

        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
