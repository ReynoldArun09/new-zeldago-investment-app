@extends('user.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600 mt-1">Stay updated with your account activity.</p>
        </div>
        
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('user.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm font-medium text-primary hover:text-indigo-700 transition-colors bg-indigo-50 px-4 py-2 rounded-lg">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3">
            <i class="ph ph-check-circle text-green-600 text-xl shrink-0"></i>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-6 flex gap-6 border-b border-gray-200">
        <a href="{{ route('user.notifications.index', ['tab' => 'unread']) }}" class="pb-3 text-sm font-semibold transition-colors {{ $tab === 'unread' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-700' }}">
            Unread
        </a>
        <a href="{{ route('user.notifications.index', ['tab' => 'history']) }}" class="pb-3 text-sm font-semibold transition-colors {{ $tab === 'history' ? 'text-primary border-b-2 border-primary' : 'text-gray-500 hover:text-gray-700' }}">
            History
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <ul class="divide-y divide-gray-50">
            @forelse($notifications as $notification)
                <li class="p-6 transition-colors {{ $notification->read_at ? 'bg-white' : 'bg-indigo-50/30' }}">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $notification->read_at ? 'bg-gray-100 text-gray-500' : 'bg-indigo-100 text-primary' }}">
                            @if(isset($notification->data['icon']))
                                <i class="ph {{ $notification->data['icon'] }} text-xl"></i>
                            @else
                                <i class="ph ph-bell text-xl"></i>
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
                            <form action="{{ route('user.notifications.mark-read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-primary hover:bg-indigo-50 transition-colors" title="Mark as read">
                                    <i class="ph ph-check text-lg"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </li>
            @empty
                <li class="p-12 text-center text-gray-500">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <i class="ph ph-bell-slash text-3xl text-gray-300"></i>
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
