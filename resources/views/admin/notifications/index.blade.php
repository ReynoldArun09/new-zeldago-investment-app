@extends('admin.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="min-h-full p-4 sm:p-6" x-data="{ openModal: false }">

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 mb-0">
        <a href="{{ route('admin.notifications.index') }}" class="px-6 py-3 text-sm font-medium border-t-2 border-t-[var(--theme-primary)] text-[var(--theme-primary)] bg-white border-r border-gray-200 flex items-center shadow-sm">
            <i class="ph ph-bell mr-2 text-lg"></i> System Notifications
        </a>
        <a href="{{ route('admin.notifications.history') }}" class="px-6 py-3 text-sm font-medium text-gray-500 bg-white hover:text-gray-700 hover:bg-gray-50 border-r border-gray-200 flex items-center transition-colors shadow-sm">
            <i class="ph ph-clock mr-2 text-lg"></i> History
        </a>
    </div>

    {{-- Main Container --}}
    <div class="bg-white rounded-none shadow-sm border border-gray-100 border-t-0 flex flex-col">
        <div class="p-5 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Notifications</h2>
            
            <div class="flex items-center gap-3">
                @if(auth('admin')->user()->unreadNotifications->count() > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-white hover:opacity-90 transition-colors bg-gray-600 px-4 py-2 rounded">
                        Mark all as read
                    </button>
                </form>
                @endif
                <button type="button" @click="openModal = true" class="text-sm font-medium text-[var(--theme-primary)] border border-[var(--theme-primary)] hover:bg-[var(--theme-primary)] hover:text-white transition-colors px-4 py-2 rounded flex items-center gap-1 bg-white">
                    <i class="ph ph-paper-plane-right font-bold"></i> Send Notification
                </button>
            </div>
        </div>

        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm whitespace-nowrap">
                <thead>
                    <tr class="text-white" style="background-color: var(--theme-primary);">
                        <th class="text-center px-5 py-3 font-medium w-16">Icon</th>
                        <th class="text-left px-5 py-3 font-medium">Notification</th>
                        <th class="text-left px-5 py-3 font-medium">Time</th>
                        <th class="text-center px-5 py-3 font-medium">Status</th>
                        <th class="text-right px-5 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($notifications as $notification)
                    <tr class="hover:bg-gray-50 transition-colors {{ $notification->read_at ? '' : 'bg-gray-50/50' }}">
                        <td class="px-5 py-4 text-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mx-auto {{ $notification->read_at ? 'bg-gray-100 text-gray-500' : 'bg-indigo-100 text-indigo-600' }}" style="{{ !$notification->read_at ? 'color: var(--theme-primary); background-color: color-mix(in srgb, var(--theme-primary) 10%, transparent);' : '' }}">
                                @if(isset($notification->data['icon']))
                                    <i class="ph {{ $notification->data['icon'] }} text-xl"></i>
                                @else
                                    <i class="ph ph-bell text-xl"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-normal min-w-[300px]">
                            <h4 class="font-bold {{ $notification->read_at ? 'text-gray-700' : 'text-gray-900' }}">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h4>
                            <p class="text-xs mt-1 {{ $notification->read_at ? 'text-gray-500' : 'text-gray-700' }}">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-500">
                            {{ $notification->created_at->diffForHumans() }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($notification->read_at)
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-bold uppercase">Read</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold uppercase">Unread</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if(!$notification->read_at)
                                <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded text-gray-400 hover:text-[var(--theme-primary)] hover:bg-gray-100 transition-colors" title="Mark as read">
                                        <i class="ph ph-check text-lg"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-300"><i class="ph ph-check-circle text-lg"></i></span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-500">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <i class="ph ph-check-circle text-3xl text-gray-300"></i>
                            </div>
                            <p class="font-medium text-gray-600">You're all caught up!</p>
                            <p class="text-sm mt-1">You have no notifications at the moment.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Showing {{ $notifications->firstItem() ?? 0 }} to {{ $notifications->lastItem() ?? 0 }} of {{ $notifications->total() }} results
            </span>
            <div>
                {{ $notifications->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        </div>
        @else
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <span class="text-sm text-gray-500">
                Showing {{ $notifications->count() }} to {{ $notifications->count() }} of {{ $notifications->count() }} results
            </span>
            <div class="flex gap-1">
                <button disabled class="px-3 py-1 border border-gray-100 rounded-md bg-gray-50 text-gray-400 text-sm">&lt;</button>
                <button class="px-3 py-1 border border-[var(--theme-primary)] rounded-md bg-[var(--theme-primary)] text-white text-sm">1</button>
                <button disabled class="px-3 py-1 border border-gray-100 rounded-md bg-gray-50 text-gray-400 text-sm">&gt;</button>
            </div>
        </div>
        @endif
    </div>

    {{-- Send Notification Modal --}}
    @include('admin.notifications.partials.send-modal')
</div>
@endsection
